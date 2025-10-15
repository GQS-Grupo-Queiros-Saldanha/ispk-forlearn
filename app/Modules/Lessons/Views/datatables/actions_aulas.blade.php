<a href="{{ route('lessons.teste-show', $aulas->id) }}" class="btn btn-info btn-sm">
    <i class="far fa-eye"></i>
</a>

<a href="{{ route('lessons.teste-edit', $aulas->id) }}" class="btn btn-warning btn-sm">
    <i class="fas fa-edit"></i>
</a>

<a href="{{ route('lessons.teste-delete', $aulas->id) }}" class="btn btn-danger btn-sm">
    <i class="fas fa-trash-alt"></i>
</a>