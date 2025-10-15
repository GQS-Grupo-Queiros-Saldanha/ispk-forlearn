<a href="{{ route('candidate.view_candidatura', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('candidate.edit_candidatura', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

<a href="{{ route('fase.anolectivo', $item->id) }}" class="btn btn-primary btn-sm" target="_blank">
    @icon('fas fa-f')
</a>