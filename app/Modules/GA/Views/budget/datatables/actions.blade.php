@php
    $contador = 0;
@endphp


@foreach ($budget_chapter as $capitulo)
    @if ($item->id == $capitulo->budget_id)
        @php
            $contador++;
        @endphp
    @endif
@endforeach


@if ($contador > 0)
    @if (auth()->user()->hasAnyPermission(['manage-budget']))
        <a href="{{ route('budget_chapter.budget', $item->id) }}" class="btn btn-info btn-sm">
            @icon('far fa-eye')
        </a>
    @endif
@endif
@php
    $last = 0;
@endphp

@foreach ($budget_last as $capitulo)
    @if ($capitulo->budget_id == $item->id)
        @php
            $last = $capitulo->code;
            
        @endphp
    @break
@endif
@endforeach


{{-- @if ($item->state == 'concluido')
@else --}}
@if (auth()->user()->hasAnyPermission(['manage-budget']))
<a href="{{ route('budget.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>
@endif
@if (auth()->user()->hasAnyPermission(['view-budget']) ||
    auth()->user()->hasAnyPermission(['manage-budget']))
@if ($contador > 0)
    <a href="{{ route('budget.reports', $item->id) }}" class="btn btn-info btn-sm" target="blank">
        @icon('fas fa-file-pdf')
    </a>
@endif
@endif
@if (auth()->user()->hasAnyPermission(['manage-budget']))
<button class='btn btn-sm btn-danger delete_budget' onclick="pegar(this)" data="{{ $item->id }}"
    data-toggle="modal" data-target="#Modalconfirmar" type="submit">
    @icon('fas fa-trash-alt')
</button>
<a href="#" data-toggle="modal" data-target="#modal-chapter" onclick="novo(this)"
    data="{{ $item->id }},{{ $item->name }},{{ $last }}" class="btn btn-success btn-sm">
    @icon('fas fa-plus')
</a>
@endif
{{-- @endif --}}
