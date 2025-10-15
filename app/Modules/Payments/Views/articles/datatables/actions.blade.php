@php
    $array=$anoLectivo[0]->id.','.$item->id
@endphp
<a href="{{ route('articles.show', $array) }}" class="btn btn-info btn-sm">
    <i class="far fa-eye"></i>
</a>

<a href="{{ route('articles.edit', $array) }}" class="btn btn-warning btn-sm">
    <i class="fas fa-edit"></i>
</a>

<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm"
        data-action="{{ json_encode(['route' => ['articles.destroy', $array], 'method' => 'delete', 'class' => 'd-inline']) }}"
        type="submit">
    <i class="fas fa-trash-alt"></i>
</button>
