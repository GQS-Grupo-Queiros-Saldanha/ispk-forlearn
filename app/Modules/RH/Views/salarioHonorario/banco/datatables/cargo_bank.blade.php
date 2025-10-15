<button data-idUser="{{$item->id_user}}" data-id="{{$item->id_rh_contrato_banco}}" class="btn btn-danger btn-sm btn-excluir" data-toggle="modal" data-type="associar" data-target="#delete-bank-contrato">
    <i class="fa fa-trash-alt"></i>
</button>
<script>
    var contrato_bank=null
    var getId_user=null
    $(".btn-excluir").click(function (e) { 
        contrato_bank=$(this).attr('data-id')
        getId_user=$(this).attr('data-idUser')
    });
    $('.btn-delete-bank-user-contrato').click(function (e) { 
       $("#delete-bank-contrato").modal('hide')
        $.ajax({
                url: 'recurso_humano-eliminar-banco-funcionario-contrato/'+contrato_bank,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data)  {
                console.log(data);
                $('.alerta-contra-excluir').prop('hidden',false)
                $('.alerta-contra-excluir').text(data)
                setTimeout(() => {
                    $('.alerta-contra-excluir').prop('hidden',true);
                    $('#contrato-banks-table').DataTable().clear().destroy();
                    get_bank_user_contrato(getId_user)
                    
                }, 2900); 
            })
    
        
    });
</script>