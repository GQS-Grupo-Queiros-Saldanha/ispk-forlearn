<a href="{{ route('plano_estudo_avaliacao.edit', $item->plano_estudo_avaliacaos_id) }}" class="btn btn-warning btn-sm">
    <i class="fas fa-edit"></i>
</a>

<button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm"
    data-action="{{ json_encode(['route' => ['plano_estudo_avaliacao.destroy', $item->plano_estudo_avaliacaos_id], 'method' => 'delete', 'class' => 'd-inline']) }}"
    type="submit">
    <i class="fas fa-trash-alt"></i>
</button>
