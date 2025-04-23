<a href="{{ route('disciplines.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('disciplines.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>
<a href="{{ route('discipline.pdf', $item->id) }}" target="_blank"class="btn btn-success btn-sm">
 
   <i class="fas fa-file-pdf"></i>
</a>

@if(auth()->user()->hasRole('superadmin'))
    <button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm"
            data-action="{{ json_encode(['route' => ['disciplines.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}"
            type="submit">
        @icon('fas fa-trash-alt')
    </button>
@endif
