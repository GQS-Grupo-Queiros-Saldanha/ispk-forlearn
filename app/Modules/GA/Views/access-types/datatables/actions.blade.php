<a href="{{ route('access-types.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('access-types.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

<button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm" data-action="{{ json_encode(['route' => ['access-types.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}" type="submit">
    @icon('fas fa-trash-alt')
</button>
