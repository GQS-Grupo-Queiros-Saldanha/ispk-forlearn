<a href="{{ route('classes.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>

<a href="{{ route('classes.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

@if (auth()->user()->hasAnyRole(['superadmin','staff_forlearn']))
    <button class="btn btn-success btn-sm" id="btnDuplicateTurma" onclick="duplicateActionClasse({{$item->id}})">
        <i class="fas fa-copy"></i>
    </button>
@endif

<button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm" data-action="{{ json_encode(['route' => ['classes.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}" type="submit">
    @icon('fas fa-trash-alt')
</button>

<script>

    function duplicateActionClasse(id_classe){
        $(".inputD").val("");
         $("#id_input_avaliation").val(id_classe);
         $("#modal-copiar-turma").modal();

    }



</script>