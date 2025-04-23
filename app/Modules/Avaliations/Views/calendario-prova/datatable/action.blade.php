<a href="{{ route('school-exam-calendar.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('school-exam-calendar.edit',['id'=>$item->id_calendario,'menu_avalicao'=>false] ) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

@if(auth()->user()->hasRole('superadmin'))
    <button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm"
            data-action="{{ json_encode(['route' => ['school-exam-calendar.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}"
            type="submit">
        @icon('fas fa-trash-alt')
    </button>
@endif
