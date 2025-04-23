
<button class="btn btn-warning btn-sm" data-id="{{ $item->id }}" onclick="edit(this)">
    @icon('fas fa-edit')
</button>

<button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm" data-action="{{ json_encode(['route' => ['special-course-editions.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}" type="submit">
  @icon('fas fa-trash-alt')
</button>