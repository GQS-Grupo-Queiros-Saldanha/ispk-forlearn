
@if (auth()->user()->hasAnyPermission(['ver_aulas']))
    <a href="{{ route('lessons.show', $item->id) }}" class="btn btn-info btn-sm">
        <i class="far fa-eye"></i>
    </a>
@endif

@if (auth()->user()->hasAnyPermission(['editar_aulas']))
    <a href="{{ route('lessons.edit', $item->id) }}" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i>
    </a>
@endif

@if (auth()->user()->hasAnyPermission(['apagar_aulas']))
    <a href="{{ route('lessons.delete', $item->id) }}" class="btn btn-danger btn-sm">
        <i class="fas fa-trash-alt"></i>
    </a>
@endif