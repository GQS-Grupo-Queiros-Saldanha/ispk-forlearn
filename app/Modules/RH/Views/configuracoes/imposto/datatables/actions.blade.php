@if ($item->status=='panding')
    <button value="{{$item->id_imposto}}" data-toggle="modal" data-type="delete" data-target="#delete_imposto"  class="btn btn-info btn-sm btn-delete-imposto"><i class="fas fa-trash-alt"></i></button>
@endif
<button data-name="{{$item->display_name}}" data-descricao="{{$item->descricao}}" value="{{$item->id_imposto}}" data-toggle="modal" data-type="editar" data-target="#editar_imposto" class="btn btn-warning btn-sm btn-editar-imposto"><i class="fas fa-edit"></i></button>
<a href="{{ route('recurso.plus-imposto', ['id'=>$item->id_imposto]) }}" class="btn btn-dark btn-sm"><i class="fas fa-plus"></i></a>
<script>
    // 
    $(".btn-delete-imposto").click(function (e) { 
        var getId=$(this).val();
        $("#formRoute_delete-imposto").attr('action','{{ route('recurso.deleteImposto')}}')
        $("#getId").val(getId)
    });
    $(".btn-editar-imposto").click(function (e) { 
        var getId=$(this).val();
        var name=$(this).attr('data-name');
        var descricao=$(this).attr('data-descricao');
        $("#idImposto").val(getId)
        $("#nameImposto").val(name)
        $("#descricaoImposto").val(descricao)
        console.log(descricao)
        $("#editarImposto").attr('hidden',false)
        $("#formRoute-Edita-imposto").attr('action','{{ route('recurso.Edita-imposto')}}')
        console.log(getId)
    });
</script>
