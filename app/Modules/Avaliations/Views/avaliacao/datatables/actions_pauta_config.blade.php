{{-- <a href="{{ route('pauta_student_config.create') }}" class="btn btn-info btn-sm">
    <i class="far fa-eye"></i>
</a> --}}

<a href="{{ route('pauta_student_config.edit',$item->id) }}" class="btn btn-warning btn-sm">
    <i class="fas fa-edit"></i>
</a>

<a href="{{ route('pauta_student_config.destroy',$item->id) }}" class="btn btn-warning btn-sm">
    <i class="fas fa-trash-alt"></i>
</a>


{{-- <button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm"
        data-action="{{ json_encode(['route' => ['pauta_student_config.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}"
        type="submit">
    <i class="fas fa-trash-alt"></i>
</button> --}}