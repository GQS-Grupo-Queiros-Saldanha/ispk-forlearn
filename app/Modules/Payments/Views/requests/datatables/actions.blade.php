<a href="{{ route('requests.show', $item->id) }}" class="btn btn-info btn-sm">
    <i class="far fa-eye"></i>
</a>

@if(auth()->user()->can('manage-requests-others'))
    <a href="{{ route('requests.edit', $item->id) }}" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i>
    </a>

    @if($item->status === 'pending')
        <button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm"
                data-action="{{ json_encode(['route' => ['requests.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}"
                type="submit">
            <i class="fas fa-trash-alt"></i>
        </button>
    @endif
@endif
