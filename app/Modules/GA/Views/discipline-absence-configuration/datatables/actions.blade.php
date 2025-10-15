<a href="{{ route('discipline-absence-configuration.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm" data-action="{{ json_encode(['route' => ['discipline-absence-configuration.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}" type="submit">
    @icon('fas fa-trash-alt')
</button>
