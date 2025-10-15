<a href="{{ route('lective-years.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('lective-years.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

<a href="{{ route('lective-years-course-curricular.show', $item->id) }}" class="btn  btn-sm" title="Definir ano curricular encerrado em cursos específico" style="background-color:#807d7d;">
    @icon('fas fa-lock')
</a>

<button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm" data-action="{{ json_encode(['route' => ['lective-years.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}" type="submit">
    @icon('fas fa-trash-alt')
</button>
