

<button data-toggle="modal" value-id="{{$item->id}}" value-display_name="{{$item->display_name}}" value-descricao="{{$item->descricao}}" data-type="editar" data-target="#editar_subsidio" class="btn btn-warning btn-sm btn-editar-subsidio"><i class="fas fa-edit"></i></button>


{{--

<button data-toggle="modal" value-id="{{$item->id}}" data-type="delete" data-target="#delete_subsidio"  class="btn btn-info btn-sm btn-delete-subsidio"><i class="fas fa-trash-alt"></i></button>

--}}


<script>
    // 
    $(".btn-delete-subsidio").click(function (e) { 
        var getId=$(this).attr('value-id');
        $("#formRoute_delete-subsidio").attr('action','{{ route('recurso.deleteFuncao')}}')
        $("#getId").val(getId)
    });



    $(".btn-editar-subsidio").click(function (e) { 
                
        var getId=$(this).attr('value-id');
        var display_name=$(this).attr('value-display_name');
        var descricao=$(this).attr('value-descricao');
        
        $("#idSubsidio").val(getId)
        $("#display_name").val(display_name)
        $("#descricao").val(descricao)

        // console.log(descricao)
        // $("#editarSubsidio").attr('hidden',false)
        $("#formRoute-Edita-subsidio").attr('action','{{ route('recurso.Edita-funcao')}}')
        // console.log(getId)
        
    });
</script>
