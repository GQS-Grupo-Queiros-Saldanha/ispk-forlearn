<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>

<a href="{{ route('users.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

@if (!auth()->user()->hasAnyPermission(['secretario_view_RH']))
    <a href="{{ route('users.edit', $item->id) }}" class="btn btn-warning btn-sm">
        @icon('fas fa-edit')
    </a>
@endif

@if(auth()->user()->hasRole('superadmin'))
    <button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm"
            data-action="{{ json_encode(['route' => ['users.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}"
            type="submit">
        @icon('fas fa-trash-alt')
    </button>
@endif

<a href="{{ route('users.generate.docente.curso.pdf', $item->id) }}" target="_blank" class="btn btn-sm btn-info btn-sm">
  @icon('fas fa-file-pdf')
</a>

{{-- o botão tem que estar aqui neste arquivo  --}}

{{--<a target="_blank" class="btn btn-info btn-sm" href="{{ route('user_requests',) }}">
    <i class="fa-solid fa-t"></i>
</a>--}}

@if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']))
<a href="{{ route('users.roles', $item->id) }}" class="btn btn-light btn-sm">
    <i class="fas fa-user-shield"></i>
    @lang('Users::roles.roles')
</a>

{{-- <a href="{{ route('users.permissions', $item->id) }}" class="btn btn-light btn-sm">
    <i class="fas fa-scroll"></i>
    @lang('Users::permissions.permissions')
</a> --}}
@endif