<a href="{{ route('documents-types.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('documents-types.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

{{-- @if(auth()->user()->hasRole('superadmin')) --}}


    <button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm" data-action="{{ json_encode(['route' => ['documents-types.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}" type="submit">
        @icon('fas fa-trash-alt')
    </button>
