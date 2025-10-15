{{--<a class="btn btn-info btn-sm" id="table-show-grade" data-id="{{ $item->id }}">--}}
{{--    <i class="far fa-eye"></i>--}}
{{--</a>--}}

<a class="btn btn-warning btn-sm" id="table-edit-grade" data-row-info="{{ $item }}" onclick="editStudentGrade(this)">
    <i class="far fa-edit"></i>
</a>

<button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm"
        data-action="{{ json_encode(['route' => ['grade.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}"
        type="submit"
        onclick="setGradeToDelete({{$item->id}})">
    <i class="fas fa-trash-alt"></i>
</button>
