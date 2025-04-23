


@if(!($item->states === 'total' || $item->states === 'parcial'))
 
    <button class="btn btn-sm btn-danger" onclick="destroy(this)" data-action="{{ route('student-special-course.destroy', $item->id) }}">
  @icon('fas fa-trash-alt')
</button>
@else
N/A
@endif
