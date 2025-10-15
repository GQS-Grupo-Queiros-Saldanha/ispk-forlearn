
<button data-user="{{$item->id_user}}" class="btn btn-info btn-sm associar_banco" data-toggle="modal" data-type="associar" data-target="#associar_contrato"
        type="submit">
    <i class="fa fa-bank"></i>
</button>


<script>
    var contagen=0;
    $(".associar_banco").click(function (e) { 
        var getUser=$(this).attr('data-user');
        //  console.log(getUser)

        var getBankData = @json($banks);
        var get_banco = JSON.parse(JSON.stringify(getBankData));
        
        var banco_data=$("#banco_data");        
        banco_data.empty();
        banco_data.append('<option selected></option>');

        $.each(get_banco, function (index, item) { 
            if (item.id_user == getUser) {                
                banco_data.append('<option value="' +item.id+ '">' +item.banco+ '</option>');
            }
        });
        banco_data.selectpicker('refresh');
        

        var getContratoData = @json($contrato);
        var get_contrato = JSON.parse(JSON.stringify(getContratoData));

        // console.log(get_contrato)
        // console.log(contagen)

        var contrato_data=$("#contato_data");        
        contrato_data.empty();
        contrato_data.empty();
        contrato_data.append('<option selected></option>');
        $.each(get_contrato, function (index, item) { 
            if (item.contrato_id_user == getUser) {                
                contrato_data.append('<option value="' +item.id+ '">' +item.name_cargo+ '</option>');
            }
        });
        contagen=contagen + 1;

        get_bank_user_contrato(getUser)
        contrato_data.selectpicker('refresh');
        $("#formRoute-Edita-bankContrato").attr('action','{{ route('recurso-humano.store-user-banco-contrato')}}')
    });

    function get_bank_user_contrato(id) {
        // console.log(id)
        // console.log(contagen)

        if (contagen>2 && contagen!=1) {
            $('#contrato-banks-table').DataTable().clear().destroy();
        }
        $('#contrato-banks-table').DataTable({
                    ajax: 'recurso_humanoa-jaxUserBankContrato/'+id,
                    buttons:[
                        'colvis'
                    ],
                    columns: [
                        {
                            data: 'DT_RowIndex', 
                            orderable: false, 
                            searchable: false
                        }, {
                            data: 'nome_user',
                            name: 'user.name',
                        }, {
                            data: 'nome_banco',
                            name: 'banco.display_name'
                        }, {
                            data: 'nome_cargo',
                            name: 'role_trans.display_name'
                        },{
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        },{
                            data: 'nome_create',
                            name: 'fullCreate_name.value',
                        },{
                            data: 'created_at',
                            name: 'rh_contrato_banco.created_at',
                        },{
                            data: 'actions',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                        targets: [4],
                            render: function ( data, type, row,meta) {
                                if(meta.col==4){
                                    var status=meta.settings.aoData[meta.row]._aData.status
                                    var id_rh_contrato_banco=meta.settings.aoData[meta.row]._aData.id_rh_contrato_banco
                                    return status=='ativo'  ? '<span class="bg-success p-1 text-white">Activo</span> ' : '<span class="bg-info p-1">Desativado</span> <a href="/pt/RH/recurso_humanos_updateAtivarBancoProcessarSalario/'+id_rh_contrato_banco+'"  class="btn btn-sm btn-dark">Ativar</a>';
                                       
                                }
                        }
                    }],
                    language: {
                        url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                    }
                });
    }

</script>