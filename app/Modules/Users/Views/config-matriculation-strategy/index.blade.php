<title>Matrícula | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Configurações de matrícula')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrícula</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Configurações</li>
@endsection
@section('selects')
    <div class="mb-2">

    </div>
@endsection
@section('body')
    <table id="configuration-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Estratégia</th>
                <th>Estado</th>
                <th>Descrição</th>
                <th>criado à</th>
                <th>Actualizado à</th>
                <th>Criado por</th>
                <th>Actualizado por</th>
                <th>Ações</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    @include('Users::config-matriculation-strategy.modal')
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {

            getConfiguration();

            function getConfiguration() {
                $('#configuration-table').DataTable({
                    ajax: '{!! route('matriculation.config.ajax') !!}',
                    buttons: [{
                        className: 'btn-primary main ml-1 rounded',
                        text: '<i class="fas fa-plus-square"></i> Criar nova configuração',
                        attr: {
                            "data-toggle": "modal",
                            "data-target": "#exampleModalCenter"
                        },
                        action: function(e, dt, node, config) {
                            document.querySelector("#exampleModalLongTitle").innerHTML =
                                "Configuração de aprovação de matrícula";
                            document.querySelector("#btn-save").innerHTML =
                                "<i class='fas fa-save'></i><span>Salvar</span>";
                         
                            // selectorStrategy("");
                            // initValueInform(10, 7, 13, 7, 14, 40, 60);
                            // formAction('{!! route('avaliacao.config.store') !!}');
                        }
                    }],
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        }, {
                            data: 'institution',
                            name: 'institution',
                        }, 
                        {
                            data: 'status_color',
                            name: 'status_color',
                        }, 
                        {
                            data: 'description',
                            name: 'description',
                        },
                        {
                            data: 'create_at',
                            name: 'create_at',
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at',
                        },
                        {
                            data: 'created_by',
                            name: 'created_by',
                        },
                        {
                            data: 'updated_by',
                            name: 'updated_by',
                        },

                        {
                            data: 'actions',
                            name: 'action',
                        },
                    ],
                    "lengthMenu": [
                        [10, 50, 100, 50000],
                        [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                    }
                });
            }

            $("#btn-save").click((e)=>{
                const Data=$("#form-matriculation-config").serialize();
               
                $.ajax({
                    url: '{!! route('save-strategy-matriculation') !!}',
                    method:'POST',
                    data: Data,
                    dataType: "JSON",
                    beforeSend:function(){
                        console.log("Antes de enviar os dados");
                        console.log(Data);
                    },
                    success: function (response) {
                        
                        console.log(response)
                        if(response==1){
                            alert("Estratégia actualizada com sucesso!");
                        }
                    }
                });
            });


        })();
    </script>
@endsection
