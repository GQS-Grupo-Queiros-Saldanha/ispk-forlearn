
<table class="table table-bordered">
    <thead>
        <th style="font-size: 8pt;" class="text-center">Curso</th>
        <th style="font-size: 8pt;" class="text-center">Código</th>
        <th style="font-size: 8pt;" class="text-center">Ano Lectivo</th>
        <th style="font-size: 8pt;" class="text-center">Regime</th>
        <th style="font-size: 8pt;" class="text-center">Turma</th>
        <th style="font-size: 8pt;" class="text-center">Avaliação</th>
    </thead>
    <tbody>
        <tr>
            <td width="25%" style="font-size: 8pt;" width="25%" style="font-size: 8pt;" class="text-center">
                {{ $discipline->course->currentTranslation->display_name }}
            </td>
            <td width="25%" style="font-size: 8pt;" class="text-center">
                {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name }}
            </td>
            <td width="25%" style="font-size: 8pt;" class="text-center"></td>
            <td width="25%" style="font-size: 8pt;" class="text-center">
                {{ $discipline->study_plans_has_disciplines->first()->discipline_period->currentTranslation->display_name }}
            </td>
            <td width="25%" style="font-size: 8pt;" class="text-center">
                {{ $class->code }}
            </td>
            <td width="25%" style="font-size: 8pt;" class="text-center">
                {{ $avaliacao[0]->nome }}
            </td>
        </tr>
    </tbody>
</table>
    @php $x = 0; $i = 1; $contaExame = 0; $contaRecurso = 0; $contaReprovado = 0; $contaAprovado = 0; @endphp

<table class="table table-bordered table-striped">
    <thead>
        <thead>
            <th style="font-size: 8pt;" class="text-center">#</th>
            <th style="font-size: 8pt;" class="text-center">Nº aluno</th>
            <th style="font-size: 8pt;" class="text-center">Nome</th>
             @for ($a = 0; $a < count($metricas); $a++)
                @if ($metricas[$a]->metrica_id != 56)
                        <th style="font-size: 8pt;" class="text-center">
                            {{ $metricas[$a]->nome }}
                        </th>
                @endif
            @endfor
            @for ($b = 0; $b < count($avaliacao); $b++)
                <th style="font-size: 8pt;" class="text-center">
                    {{ $avaliacao[$b]->nome }}
                </th>
            @endfor
            <th style="font-size: 8pt;" class="text-center">Observações</th>
        </thead>
        <tbody>
            @for ($c = 0; $c < count($students); $c++)
                <tr>
                    @php $flag = true; $id_user = $students[$c]->user_id;  $nota_final = 0; @endphp
                    <td style="font-size: 8pt;" class="text-center">
                        {{ $i++ }}
                    </td>
                    <td style="font-size: 8pt;" class="">
                        {{ $students[$c]->student_number }}
                    </td>
                    <td style="font-size: 8pt;" class="">
                        {{ $students[$c]->user_name }}
                    </td>
                    @for ($d = 0; $d < count($metricas); $d++)
                        @php $metrica_id = $metricas[$d]->metrica_id; $flag = true; @endphp

                            @for ($e = 0; $e < count($grades); $e++)
                                @if ($grades[$e]->users_id == $id_user && $grades[$e]->metricas_id == $metrica_id)
                                    @php $flag = false; @endphp

                                    @if ($metrica_id != 56)

                                        @if ($grades[$e]->nota == null)
                                                <td> F </td>
                                        @else
                                            <td> {{ $grades[$e]->nota }} </td>
                                        @endif

                                    @endif

                                        @php $nota_final += ($grades[$e]->nota * $metricas[$d]->percentagem) / 100; @endphp
                                        @php $nota_final = number_format($nota_final); @endphp

                                @endif
                            @endfor
                            @if ($flag)
                                <td> - </td>
                            @endif
                    @endfor
                    <td> {{ $nota_final }}</td>
                    @if ($disciplineHasMandatoryExam->exam == 1)
                        @for ($f = 0; $f < count($avaliacao); $f++)
                            @if ($avaliacao[$f]->id == 21 && $nota_final >= 6.5)
                                <td> Exame @php $contaExame++; @endphp </td>
                            @elseif($avaliacao[$f]->id == 21 && $nota_final <= 6)
                                <td> Recurso @php $contaRecurso++; @endphp </td>
                            @elseif($avaliacao[$f]->id == 23 && $nota_final <= 9)
                                <td> Recurso @php $contaRecurso++; @endphp </td>
                            @elseif($avaliacao[$f]->id == 23 && $nota_final >= 10)
                                <td> Aporovado @php $contaAprovado++; @endphp </td>
                            @elseif($avaliacao[$f]->id == 24 && $nota_final >= 10)
                                <td> Aporovado @php $contaAprovado++; @endphp </td>
                            @elseif($avaliacao[$f]->id == 24 && $nota_final < 10)
                                <td> Reprovado</td>
                            @endif
                        @endfor
                    @else
                            @for ($f = 0; $f < count($avaliacao); $f++)
                            @if ($avaliacao[$f]->id == 21 && $nota_final >= 6.5 && $nota_final <= 13)
                                <td> Exame  @php $contaExame++; @endphp </td>
                            @elseif($avaliacao[$f]->id == 21 && $nota_final >= 13.5 && $nota_final <= 20)
                                <td> Aprovado @php $contaAprovado++; @endphp</td>
                            @elseif($avaliacao[$f]->id == 21 && $nota_final >= 0 && $nota_final <= 6)
                                <td> Recurso @php $contaRecurso++; @endphp </td>
                            @elseif($avaliacao[$f]->id == 23 && $nota_final >= 10)
                                <td> Aprovado  @php $contaAprovado++; @endphp</td>
                            @elseif($avaliacao[$f]->id == 23 && $nota_final < 10)
                                <td> Recurso @php $contaRecurso++; @endphp</td>
                            @elseif($avaliacao[$f]->id == 24 && $nota_final >= 10)
                                <td> Aporovado @php $contaAprovado++; @endphp </td>
                            @elseif($avaliacao[$f]->id == 24 && $nota_final < 10)
                                <td> Reprovado</td>
                            @endif
                        @endfor
                    @endif

                </tr>
            @endfor

            {{--@for ($b = 0; $b < count($students); $b++)
            <tr>
                pedro
                @php $flag = true; @endphp
                <td style="font-size: 8pt;" class="text-center">
                    {{ $i++ }}
                </td>
                <td style="font-size: 8pt;" class="text-center" width="100px">
                    {{ $students[$b]->student_number }}
                </td>
                <td style="font-size: 8pt;">
                    {{ $students[$b]->user_name}}
                </td>
                @for ($c = 0; $c < count($example); $c++)
                @php
                    $x = $x + 1;
                    $x = $x % count($metrics)
                    asps
                @endphp
                @for ($d = 0; $d < count($grades); $d++)
                    @if ($grades[$d]->users_id == $students[$b]->user_id && $grades[$d]->metricas_id == $example[$c]->metrica_id)
                        @php $flag = false; @endphp
                        <td style="font-size: 8pt;" class="text-right">
                           {{ $grades[$d]->nota }}
                        </td>
                    @endif
                @endfor
                    @if ($example[$c]->avaliacaos_id != $example[$x]->avaliacaos_id)
                        @for ($e = 0; $e < count($finalGrades); $e++)
                            @if ($finalGrades[$e]->users_id == $students[$b]->user_id && $finalGrades[$e]->avaliacaos_id == $example[$c]->avaliacaos_id)
                                @php $flag = false; @endphp
                                <td style="font-size: 8pt;" class="text-right">
                                  {{ round($finalGrades[$e]->nota_final) }}
                                </td>
                            @endif
                        @endfor
                    @endif

                    @if ($flag)
                        <td style="font-size: 8pt;"> - </td>
                    @endif
                @endfor
                asap

                @for ($f = 0; $f < count($gradeWithPercentage); $f++)
                    @if ($gradeWithPercentage[$f]->user_id == $students[$b]->user_id)
                            <td style="font-size: 8pt;" class="text-right">
                                {{ $gradeWithPercentage[$f]->nota }}
                            </td>
                    @endif

                @endfor
            </tr>
            @endfor--}}
        </tbody>
    </thead>
</table>

<br><br>

<div class="col-2">
    <table class="table table-bordered table-striped">
        <thead>
            <th colspan="2" class="text-center">
                Totais
            </th>
        </thead>
        <tbody>
            <tr>
                <td style="font-size: 8pt;">Estudantes</td>
                <td style="font-size: 8pt;"  class="text-right">
                    {{ count($students)}}
                </td>
            </tr>
            <tr>
                <td style="font-size: 8pt;">Exame</td>
                <td style="font-size: 8pt;"  class="text-right">
                    {{ $contaExame }}
                </td>
            </tr>
            <tr>
                <td style="font-size: 8pt;" width="100px;">Reprovado</td>
                <td style="font-size: 8pt;" class="text-right">

                </td>
            </tr>
            <tr>
                <td style="font-size: 8pt;" width="100px;">Recurso</td>
                <td style="font-size: 8pt;" class="text-right">
                    {{ $contaRecurso }}
                </td>
            </tr>
            <tr>
                <td style="font-size: 8pt;" width="100px;">Aprovado (a)</td>
                <td style="font-size: 8pt;" class="text-right">
                    {{ $contaAprovado }}
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="col-12">
    <table class="table-borderless">
        <thead>
            <th colspan="2" style="font-size: 9pt;">
                Assinaturas
            </th>
        </thead>
        <tbody>
            <tr>
                <td style="font-size: 9pt;">Docente: ________________________________________________________________________.</td>
                <td style="font-size: 9pt;">Pelo gabinete de termos: ____________________________________________________________________.</td>
            </tr>
        </tbody>
    </table>
</div>