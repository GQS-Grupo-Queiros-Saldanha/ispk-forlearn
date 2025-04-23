

@if (auth()->user()->hasAnyPermission(['editar_horario']))
<a href="{{ route('schedules.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('schedules.edit', $item->id) }}" class="btn btn-warning btn-sm" >
    @icon('fas fa-edit')
</a>
@endif

@if (auth()->user()->hasAnyPermission(['eliminar_horario']))
<button class="btn btn-sm btn-danger" data-toggle="modal" data="delete" data-target="#modal_confirm" data-action="{{ json_encode(['route' => ['schedules.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}" type="submit">
    @icon('fas fa-trash-alt')
</button>

@endif

<a href="{{ route('schedules.pdf', $item->id) }}" class="btn btn-dark btn-sm" target="_blank">
    @icon('far fa-file-pdf')
</a>
