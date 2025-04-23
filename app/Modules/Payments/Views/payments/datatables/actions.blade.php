<a href="{{ route('account.show', $item->id) }}" class="btn btn-info btn-sm">
    <i class="far fa-eye"></i>
</a>

{{--<a href="{{ route('articles.edit', $item->id) }}" class="btn btn-warning btn-sm">--}}
{{--    <i class="fas fa-edit"></i>--}}
{{--</a>--}}

<button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm"
        data-action="{{ json_encode(['route' => ['account.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}"
        type="submit">
    <i class="fas fa-trash-alt"></i>
</button>
