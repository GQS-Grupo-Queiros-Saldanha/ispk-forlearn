<a href="{{ route('users.show', $item->id_usuario) }}" class="btn btn-warning btn-sm editChange" target="_blank"
    id="#" title="Ver perfil">
    @icon('fas fa-user')
</a>


@if ($item->cargo == null)
 <a href="{{ route('academic-path-imported.percurso', $item->id_usuario) }}" target="_blank" class="btn btn-success btn-sm "
        id="" title="ver Percurso">
        @icon('fa-solid fa-p')
    </a>
@endif
