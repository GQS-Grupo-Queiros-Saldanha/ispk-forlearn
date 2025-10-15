<button value="{{$item->id_webhook}}" data-toggle="modal"   data-type="delete" data-target="#delete_config_cliente" value=""  class=" p-2 btn btn-info btn-sm btn-delete-config-cliente"><i class="fas fa-trash-alt"></i></button>
<button value="{{$item->id_webhook}}" data-nome_cliente="{{$item->cliente}}" data-endpoint="{{$item->endpoint}}" data-status="{{$item->status}}" data-toggle="modal"  data-type="editar" data-target="#editar_config_cliente" class=" p-2 btn btn-warning btn-sm btn-config-cliente"><i class="fas fa-edit"></i></button>


<script>
    $('.btn-delete-config-cliente').click(function (e) { 
        var getId=$(this).val();
        $("#id_webhook_cliente").val(getId);
    });

    $('.btn-config-cliente').click(function (e) { 
        var getId=$(this).val();
        var nome_cliente=$(this).attr('data-nome_cliente')
        var endpoint=$(this).attr('data-endpoint')
        var status=$(this).attr('data-status')
        console.log(status);

        $("#endpoint_edit").val(endpoint);
        $("#nome_cliente_edit").val(nome_cliente);
        $("#id_webhook_cliente_edit").val(getId);
        if (status=='ativo') {
            $('input[name="status"]').prop("checked",  true);
        }else{
            $('input[name="status"]').prop("checked",  false);

        }
        
    });
</script>
