{{-- Verificar Se a Pauta Final foi Publicada --}}

@php

$total_final = 0;
$link = '';
$coordenador = '';
$prof = '';

@endphp

@foreach ($dados as $item)
@if ($item->id_turma == $allDiscipline->id_turma 
&& $item->id_disciplina == $allDiscipline->id_disciplina_no_plano
&& isset($item->path))

@php
$total_final = 1;
$link = 'pauta-geral/'.$item->pauta_id;
@endphp
@break;
@else
@endif
@endforeach

{{-- Verificar se o coordenador logado é coordenador das disciplina em questão --}}

@foreach ($coordinator_course as $item)
@if ($item->courses_id == $allDiscipline->curso_id && $item->user_id == auth()->user()->id)
@php $coordenador="existe" @endphp
@else
@endif
@endforeach

{{-- Verificar se o professor logado é o professor da Disciplina --}}

@foreach ($professor as $item)
@if ($item->disciplina == $allDiscipline->id_disciplina_no_plano)
@php $prof="existe" @endphp
@endif
@endforeach


@if ($total_final == 1)
@if (auth()->user()->hasAnyRole(['teacher', 'superadmin', 'coordenador-curso', 'Chefe_do_gabinete_de_termos', 'staff_matriculas']))
@if (
$prof == 'existe' ||
$coordenador == 'existe' ||
auth()->user()->hasAnyRole(['superadmin', 'Chefe_do_gabinete_de_termos', 'staff_matriculas']))
<a tabindex="0" data-bs-toggle="tooltip" data-html="true"
    target="_blank" href="{{ $link }}" class="btn "
    style="line-height: 1.3;padding: 0px;">
    <i class="fas fa-file-pdf"></i>
</a>
@else
@endif
@endif
<i class="fa fa-check-circle p-1 texto-verde " aria-hidden="true"></i>
<bdo style="font-size: 1px;">P</bdo>
@else
<i class="fa fa-circle p-1 texto-vermelho" aria-hidden="true"></i>
<bdo style="font-size: 1px;">-</bdo>
@endif