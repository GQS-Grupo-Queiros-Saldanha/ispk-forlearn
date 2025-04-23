
<button data-toggle="modal" data-id="{{$item->id_funcionario}}" data-type="editar" data-target="#verSubsidio-funcionario" class="btn btn-dark btn-sm btn-editar-subsidio"><i class="fas fa-s"></i></button>
{{-- <button data-toggle="modal"   data-type="delete" data-target="#delete_subsidio"  class="btn btn-info btn-sm btn-delete-subsidio"><i class="fas fa-trash-alt"></i></button> --}}

<script>
    var qtdClick=0;
    var getRequisicao=false;
    $(".btn-editar-subsidio").click(function (e) { 
        var id_funcionario=$(this).attr('data-id');
        qtdClick+=1
       if (qtdClick>2) {
            $('#cargo-subsidioFuncionario').DataTable().clear().destroy();
            ajaxSubsidio(id_funcionario);
       } else {
            ajaxSubsidio(id_funcionario);
        }
        
    });

    function ajaxSubsidio(id_funcionario) {
        $('#cargo-subsidioFuncionario').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: 'recuros_ajaxSubsidioFuncionario/'+id_funcionario,
                        columns: [
                            {
                                data: 'DT_RowIndex', 
                                orderable: false, 
                                searchable: false
                            },{
                                data: 'name_role',
                                name: 'role_trans.display_name'
                            },{
                                data: 'subsidios',
                                name: 'subsidio',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'status_contrato',
                                name: 'fun_with_type_cont.status_contrato',
                                searchable: false
                            }
                            // ,{
                            //     data: 'actions',
                            //     name: 'action',
                            //     orderable: false,
                            //     searchable: false
                            // } 
                        ],
                        columnDefs: [{
                                targets: 3,
                                    render: function ( data, type, row ) {
                                        if (data!= 'ativo'){
                                            return '<span class="bg-info p-1">N/A</span>';             
                                        } else {
                                            return '<span class="bg-success p-1 text-white">Activo</span>';
                                        }
                                    }
                                }],
                            
                        "lengthMenu": [ [10, 50, 100, 50000],  [10, 50, 100, "Todos"]
                            ],
                        language: {
                            url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                        },
        });

       

        
    }
    
</script>