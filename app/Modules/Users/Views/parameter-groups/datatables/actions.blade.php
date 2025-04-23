<a href="{{ route('parameter-groups.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('parameter-groups.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

<button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm" data-action="{{ json_encode(['route' => ['parameter_group.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}" type="submit">
    @icon('fas fa-trash-alt')
</button>

<a href="{{ route('parameter-groups.parameter_order', $item->id) }}" class="btn btn-info btn-sm">
    <i class="fas fa-bars"></i>&nbsp;@lang('Users::parameter-groups.parameters')
</a>
