<a href="{{ route('budget_chapter.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>


@if ($item->state == 'concluido') 
@else
    <a href="{{ route('budget_chapter.edit', $item->id) }}" class="btn btn-warning btn-sm">
        @icon('fas fa-edit')
    </a>
 
    <button class='btn btn-sm btn-danger delete_budget' onclick="pegar(this)" data="{{$item->id}}"  data-toggle="modal" data-target="#Modalconfirmar"
        type="submit">
        @icon('fas fa-trash-alt')
    </button>
@endif


