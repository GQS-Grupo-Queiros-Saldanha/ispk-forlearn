@php
    $currentUserIsAuthorized = auth()->user()->hasAnyRole(['coordenador-curso','superadmin', 'staff_forlearn', 'staff_inscrições', 'staff_matriculas']);
@endphp

@if ($user->hasAnyRole(['coordenador-curso']))
    <div class="@if (!isset($large)) col col-6 @else large-form form-group col @endif">
        @if (isset($large))
            <label class="">Coordenador do curso de</label>
        @else
            <h5 class="card-title mb-3">Coordenador do curso de</h5>
        @endif
        @if ($action !== 'show' && $currentUserIsAuthorized)
            {{ Form::bsLiveSelect('coodinator-course[]', $courses, $action === 'create' ? old('coodinator-course[]') : $coordinatorCourse->pluck('courses_id'), ['multiple', 'placeholder' => 'Nenhum selecionado', 'required'] ) }}
        @else
            @forelse ($coordinatorCourse as $item)
                {{ $item->display_name }}
            @empty
                N/A
            @endforelse

        @endif
    </div>
@endif
