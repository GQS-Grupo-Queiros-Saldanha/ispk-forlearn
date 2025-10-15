@section('title',"Configuração-client-api")


@extends('layouts.backoffice')
<style>
    .user-profile-image {
       width: 200px !important;
    }
    input#name::placeholder {
        color: red;
    }
    input#full_name::placeholder{
        color: red;
    }
    input#id_number::placeholder{
        color: red;
    }
</style>
@section('content')
    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row -- mb-2">
                    <div class="col-sm-10">
                        <h1 class="m-0 text-dark">
                            CONFIGURAÇÃO CLIENTE API [webhook]
                        </h1>
                    </div>
                    <div class="col-sm-6">
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    ×
                                </button>
                                <h5>@choice('common.error', $errors->count())</h5>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card">

                            <form method="POST" action="{{ route('criar.webhook-servico-entidade') }}" class="pb-4">
                                @csrf
                                    <div class="card-body row pb-0">
                                        <div class="form-group col-md-6">  
                                            <label>Empresa / Organização</label>
                                            <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" id="entidade" data-actions-box="false" data-selected-text-format="values" name="entidade" tabindex="-98">
                                                <option selected></option>  
                                                @foreach ($getEntidade as $item)
                                                        <option value="{{$item->id}}">{{$item->client}}</option>  
                                                @endforeach
                                            </select>                                          
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Serviço Notificação</label>
                                            <select data-live-search="true" class="selectpicker form-control form-control-sm" required="" id="servico" data-actions-box="false" data-selected-text-format="values" name="servico" tabindex="-98">
                                                <option selected></option>  
                                                <option value="matricula">Notificar nova matricula</option>  
                                            </select>     
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="inputTelef1">Endpoint</label>
                                            <input required  type="text" class="form-control" name="endpoint" id="endpoint" placeholder="Digite a URL">
                                        </div>
                                        <div class="form-group col-md-6">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="col-5 btn btn-lg btn-success mb-3" >
                                                @icon('fas fa-plus-circle')
                                                Gravar
                                            </button>
                                        </div>
                                        
                                    </div>
                                    

                            </form>
                            <div class="container-fluid ml-2 mr-2 mt-3" >
                                <table  id="table-client-webhook"  class="table table-striped table-hover" >
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Cliente</th>
                                            <th>Serviço</th>
                                            <th>Endpoint</th>
                                            <th>Status</th>
                                            <th>Criado aos</th>
                                            <th>Create por </th>
                                            <th>Acções</th>
    
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>



  <!-- Modal -->
  <div class="modal fade" id="delete_config_cliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Informação</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Deseja Eliminar este cliente sobre na configuração de webhook ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">Cancelar</button>
          <form action="{{ route('delete.config-client-webhook') }}" method="post">
            @csrf
            <input type="hidden" name="id_webhook_cliente" id="id_webhook_cliente">
            <button type="submit" class="rounded btn btn-success">OK</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  
 
  <!-- Modal -->
  <div class="modal fade" id="editar_config_cliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Editar cliente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST" action="{{ route('editar.configuracao-cliente') }}">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="id_webhook_cliente_edit" id="id_webhook_cliente_edit">

                <div class="form-group">
                <label for="exampleInputEmail1">Cliente</label>
                <input type="text" class="form-control" id="nome_cliente_edit" name="nome_cliente" aria-describedby="emailHelp" readonly>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Servico de notificação</label>
                    <select data-live-search="true" class="selectpicker form-control form-control-sm" required="" id="servico_edit" data-actions-box="false" data-selected-text-format="values" name="servico_edit" tabindex="-98">
                        <option selected></option>  
                        <option value="matricula">Notificar nova matricula</option>  
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Endpoint</label>
                    <input type="text" class="form-control" id="endpoint_edit" name="endpoint">
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="status" name="status">
                    <label class="form-check-label" for="exampleCheck1">Ativar serviço ?</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success rounded">Gravar</button>
            </div>
        </form>
      </div>
    </div>
  </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>

        // console.clear();
        $('#table-client-webhook').DataTable({
                processing:true,
                serverSide:true,
                // destroy: true,
                ajax: 'ajax-table-client-webhook',
                columns: [
                    {
                        data: 'DT_RowIndex', 
                        orderable: false, 
                        searchable: false
                    },{
                        data:'cliente',
                        name:'cliente'
                    },{
                        data: 'servico',
                        name: 'servico'
                    }, {
                        data: 'endpoint',
                        name: 'endpoint'
                    }, {
                        data: 'status',
                        name: 'status'
                    },{
                        data: 'created_at',
                        name: 'created_at'
                    },{
                        data: 'created_by',
                        name: 'created_by'
                    }, {
                        data: 'actions', 
                        name: 'actions',
                        orderable: false, 
                        searchable: false
                    } 
                ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                },
            });
        
    </script>
@endsection


