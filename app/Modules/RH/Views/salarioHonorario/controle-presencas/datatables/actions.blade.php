


<button data-toggle="modal" value-id="{{$item->id}}" value-data="{{$item->data}}" value-entrada="{{$item->entrada}}" value-saida="{{$item->saida}}" value-funcionario_id="{{$item->funcionario_id}}" value-falta="{{$item->falta}}" data-type="editar" data-target="#editar_horasLaboral" class="btn btn-warning btn-sm btn-editar-horasLaboral"><i class="fas fa-edit"></i></button>

<button data-toggle="modal"  value="{{$item->id}}" data-type="delete" data-target="#delete_horasLaboral"  class="btn btn-info btn-sm btn-delete-horasLaboral"><i class="fas fa-trash-alt"></i></button>


<script>
    
    $(".btn-delete-horasLaboral").click(function (e) { 
        var getId=$(this).attr('value');
        $("#formRoute_delete-horasLaboral").attr('action','delete_controlePresenca/'+getId)
        $("#getId").val(getId)

    });


    $(".btn-editar-horasLaboral").click(function (e) { 
        // console.log(1254);
        
        var getId=$(this).attr('value-id');
        var getData=$(this).attr('value-data');
        var getEntrada=$(this).attr('value-entrada');
        var getSaida=$(this).attr('value-saida');        
        var getFuncionario_id=$(this).attr('value-funcionario_id');        
        var getFalta=$(this).attr('value-falta');        
        
        $("#id_presence").val(getId)
        $("#data").val(getData)
        $("#entrada").val(getEntrada)
        $("#saida").val(getSaida)        
        $("#funcionario_id").val(getFuncionario_id)        
        $("#falta").val(getFalta)        
        // $("#arquivo").val(getFuncionario_id)        
        
        $("#editarhorasLaboral").attr('hidden',false)
        $("#formRoute-Edita-horasLaboral").attr('action','edit_controlePresenca')
        console.log(getFalta)
        
    });


</script>

