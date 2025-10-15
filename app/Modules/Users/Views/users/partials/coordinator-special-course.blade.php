@php
    $currentUserIsAuthorized = auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_inscrições', 'staff_matriculas']);
    $isCoordenador = !(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']) 
            || auth()->user()->hasAnyPermission(['criar_docente'])) ? 1 : 0;
            $otherProfile = $user->id != auth()->user()->id;
            $show = !($isCoordenador && $otherProfile);
    @endphp

@if ($user->hasAnyRole(['coordenador-curso-profissional']) &&  $show)
    <div class="@if (!isset($large)) col col-6 @else large-form form-group col @endif">
        @if (isset($large))
            <label class="">Encarregado do curso profissional de</label>
        @else
            <h5 class="card-title mb-3">Encarregado do curso profissional de</h5>
        @endif
        @if ($action !== 'show' && $currentUserIsAuthorized)
            {{ Form::bsLiveSelect('coodinator-special-course[]', $special_courses, $action === 'create' ? old('coodinator-special-course[]') : $coordinatorSpecialCourse->pluck('courses_id'), ['multiple', 'placeholder' => 'Nenhum selecionado', 'required'] ) }}
        @else
            @forelse ($coordinatorSpecialCourse as $item)
                {{ $item->display_name }}
            @empty
                N/A
            @endforelse

        @endif
    </div>
@endif
