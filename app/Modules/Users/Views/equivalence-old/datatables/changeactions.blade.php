{{-- <a href="#" class="btn btn-info btn-sm viewBtn">
    @icon('fas fa-eye')
</a>
<a href="#" class="btn btn-warning btn-sm editChange">
    @icon('fas fa-edit')
</a> --}}
<a href="{{route('matriculations.show', $item->id_matricula)}}" target="_blank" class="btn btn-success btn-sm editChange" title="Ver matrícula">
    @icon('fas fa-book')
</a>
