<button data-toggle="modal" data-target="#funCargoRescindido" data-idRescisoes="{{ $item->id_rescisoes }}"
    class="btn btn-info btn-sm btn-ver-rescisao"><i class="fas fa-eye"></i></button>

<script>
    var getboolen = false
    $(".btn-ver-rescisao").click(function() {
        var getid_rescisoes = $(this).attr('data-idRescisoes');
        console.log(getid_rescisoes)
        if (getboolen == false) {
            getboolen = true
            console.log(getboolen)
            getRescisaoContrato(getid_rescisoes)
        } else {
            $('#cargo-rescisao-contrato').DataTable().clear().destroy();
            getRescisaoContrato(getid_rescisoes)
            console.log(getboolen)

        }

        function getRescisaoContrato(getid_rescisoes) {
            $('#cargo-rescisao-contrato').DataTable({
                processing: true,
                serverSide: true,
                ajax: 'recurso_ajaxCargo_rescisao_contrato/' + getid_rescisoes,
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'cargo',
                    name: 'role_trans.display_name'
                }, {
                    data: 'data_inicio_conrato',
                    name: 'fun_with_type_cont.data_inicio_conrato'
                }, {
                    data: 'data_fim_contrato',
                    name: 'fun_with_type_cont.data_fim_contrato'
                }, {
                    data: 'criado_por',
                    name: 'full_created.value'
                }, {
                    data: 'rescindido_por',
                    name: 'full_rescindido_por.value'
                }, {
                    data: 'arquivo',
                    name: 'doc_recurso_humano.arquivo'
                }, {
                    data: 'rescindido_ao',
                    name: 'rescisoes.update_at'
                }],
                columnDefs: [{
                    targets: 6,
                    render: function(data, type, row) {
                        if (data != null) {
                            return '<a href="view-file/documento_userRH/' + data +
                                '"  class="btn btn-sm btn-info link-arquivo"><i class="fas fa-file-upload" aria-hidden="true"></i></a>';
                        } else {
                            return '<p>N/A</p>';
                        }
                    }
                }],
                "lengthMenu": [
                    [10, 50, 100, 50000],
                    [10, 50, 100, "Todos"]
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                },
            });
        }
    });
</script>
