@php
    $currentUserIsAuthorized = auth()->user()->hasAnyRole(['coordenador-curso','superadmin', 'staff_forlearn', 'staff_inscrições', 'staff_matriculas','rh_chefe','rh_assistente']);
    $ids = [];
    foreach($userDepartment as $item)
     array_push($ids, $item->departments_id);    
@endphp
@if ($user->hasAnyRole(['teacher']))
    <div class="@if (!isset($large)) col col-6 @else large-form form-group col @endif">
        @if (isset($large))
            <label class="">Departamento</label>
        @else
            <h5 class="card-title mb-3">Departamento</h5>
        @endif
        @if ($action !== 'show' && $currentUserIsAuthorized)
            {{-- Form::bsLiveSelect('departments', $departments,'', ['placeholder' => 'Nenhum selecionado'] ) --}}
            <select class="form-control selectpicker" data-live-search="true" id="departments" data-actions-box="false" data-selected-text-format="values" name="departments[]" multiple>
                <option value=""disabled>Nenhum selecionado</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @if(in_array($department->id, $ids)) selected @endif> {{ $department->currentTranslation->display_name }}</option>
                @endforeach
            </select>    
        @else
            @forelse ($userDepartment as $item)
                {{ $item->display_name }}
            @empty
                N/A
            @endforelse
        @endif
    </div>
@endif
