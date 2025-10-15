<a href="{{ route('tipo_metrica.show', $item->id) }}" class="btn btn-info btn-sm">
    <i class="far fa-eye"></i>
</a>

{{--@if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']))--}}
    <a href="{{ route('tipo_metrica.edit', $item->id) }}" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i>
    </a>
{{--@endif--}}

<button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm"--}}
        data-action="{{ json_encode(['route' => ['tipo_metrica.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}"
        type="submit">
    <i class="fas fa-trash-alt"></i>
</button>
