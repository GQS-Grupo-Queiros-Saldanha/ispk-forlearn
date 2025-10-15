<button data-toggle="modal" value-id="{{$item->subsidio_id}}" value-display_name="{{$item->display_name}}" value-descricao="{{$item->descricao}}" data-type="editar" data-target="#editar_subsidio" class="btn btn-warning btn-sm btn-editar-subsidio"><i class="fas fa-edit"></i></button>

{{--
@if ($item->status=='panding')
    <button data-toggle="modal" value="{{$item->subsidio_id}}" data-type="delete" data-target="#delete_subsidio"  class="btn btn-info btn-sm btn-delete-subsidio"><i class="fas fa-trash-alt"></i></button>
@endif
--}}

<script>
    $(".btn-delete-subsidio").click(function (e) { 
        var getId=$(this).val();
        $("#formRoute_delete-subsidio").attr('action','{{ route('recurso.deleteSubsidio')}}')
        $("#getId").val(getId)
    });

    $(".btn-editar-subsidio").click(function (e) { 
        
        var getId=$(this).attr('value-id');
        var display_name=$(this).attr('value-display_name');
        var descricao=$(this).attr('value-descricao');
        
        $("#idSubsidio").val(getId)
        $("#nameSubsidio").val(display_name)
        $("#descricaoSubsidio").val(descricao)

        // console.log(descricao)
        // $("#editarSubsidio").attr('hidden',false)
        $("#formRoute-Edita-subsidio").attr('action','{{ route('recurso.Edita-subsidio')}}')
        // console.log(getId)
        
    });
</script>