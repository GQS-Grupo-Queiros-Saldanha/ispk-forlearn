<a href="{{ route('old_student.add', $item->id) }}" class="btn btn-warning btn-sm mb-3">
    <i class="fas fa-plus"></i>
</a>
<a href="{{ route('academic-path-imported.percurso', ['studentId' => $item->id]) }}" target="_blank" class="btn btn-success btn-sm "
    id="" title="ver Percurso">
    @icon('fa-solid fa-p')
</a>
