<a href="{{route('matriculations.show', $item->id_matricula)}}" target="_blank" class="btn btn-success btn-sm editChange" title="Ver matrícula">
    @icon('fas fa-book')
</a>

@if($item->state!="total")
student_normal_courses_change.delete
<a href="{{route('student_normal_courses_change.delete', $item->id)}}" data-id="{{$item->id}}" data-student="{{$item->student}}" class="btn btn-danger btn-sm editChangeDelete" title="Eliminar pedido de transferência" data-toggle="modal" data-type="anular_matricula" data-target="#aconfirmDeleteModal">
    @icon('fas fa-trash') 
</a>

@endif