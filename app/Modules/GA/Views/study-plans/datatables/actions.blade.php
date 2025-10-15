<a href="{{ route('study-plans.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('study-plans.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

<button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm" data-action="{{ json_encode(['route' => ['study-plans.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}" type="submit">
   @icon('fas fa-trash-alt')
</button>

<a href="{{ route('study-plans.pdf', $item->id) }}" target="_blank" class="btn btn-sm btn-info btn-sm">
                                    @icon('fas fa-file-pdf')
                                   
</a> 