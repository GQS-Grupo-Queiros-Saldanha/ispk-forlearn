

<a href="{{ route('special-courses.show', $item->id) }}" class="btn btn-info btn-sm">
  @icon('far fa-eye')
</a>

<a href="{{ route('special-courses.edit', $item->id) }}" class="btn btn-warning btn-sm">
  @icon('fas fa-edit')
</a>



<button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm" data-action="{{ json_encode(['route' => ['special-courses.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}" type="submit">
  @icon('fas fa-trash-alt')
</button>

<a href="{{ route('sce_list', $item->id) }}" class="btn btn-success btn-sm">
 E
</a>