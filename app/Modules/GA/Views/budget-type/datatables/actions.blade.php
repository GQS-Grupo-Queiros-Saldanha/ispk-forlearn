<a href="{{ route('budget_type.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>
<a href="{{ route('budget_type.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

<button class='btn btn-sm btn-danger delete_budget' onclick="pegar(this)" data="{{ $item->id }}" data-toggle="modal"
    data-target="#Modalconfirmar" type="submit">
    @icon('fas fa-trash-alt')
</button>
