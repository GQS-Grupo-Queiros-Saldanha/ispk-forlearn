@section('title',__('RH-recurso humanos'))
@extends('layouts.backoffice')
@section('styles')
@parent
@endsection
@section('content')
<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
<style>
    .list-group li button {
        border: none;
        background: none;
        outline-style: none;
        transition: all 0.5s;
    }

    .list-group li button:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        font-weight: bold
    }

    .subLink {
        list-style: none;
        transition: all 0.5s;
        border-bottom: none;
    }

    .subLink:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        border-bottom: #dfdfdf 1px solid;
    }
</style>

<!-- Modal  que apresenta a loande do  site -->
<div style="z-index: 1900" class="modal fade modal_loader" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
    </div>
</div>


<!-- Modal  que apresenta a opção de eliminar -->
<div class="modal fade" id="delete_subsidio" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Informação!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Caro utilizador deseja eliminar este?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <form id="formRoute_delete-subsidio" method="POST" action="">
            @csrf
              <input type="hidden" name="getId" id="getId">
            <button type="submit" class="btn btn-primary">Ok</button>
          </form>
        </div>
      </div>
    </div>
</div>


<!-- Modal  que apresenta a opção de eliminar _subsidioImposto -->
<div class="modal fade" id="delete_subsidioImposto" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Informação!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Caro utilizador deseja eliminar este?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="button"  class="btn btn-primary btn-deleteSubImposto">Ok</button>
        </div>
      </div>
    </div>
</div>


<!-- Modal para editar o subsidio  -->
<div class="modal fade" id="editar_subsidio" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-xl" role="document">
      <div class="modal-content" style="z-index: 99999;border-top-left-radius: 10px;border-top-right-radius: 10px ">
        <div style="background:#7eaf3e;width: 100%;border-top-left-radius: 15px;border-top-right-radius: 15px;height: 5px;" class="m-0" ></div>
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Editar Subsídio</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div>
        
        <div class="modal-body">
            <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                <div class="col-12 mb-4 ">
                    <form id="formRoute-Edita-subsidio" method="POST" action="" class="pb-4">
                        @csrf
                        <div id="editarSubsidio">
                            <div class="form-group col-md-12">
                                <label for="inputEmail4">Nome do subsídio</label>
                                <input required type="text" class="form-control" name="nameSubsidio" id="nameSubsidio" placeholder="Digite o nome do subsídio Exp.: SS">
                                <input  type="hidden" class="form-control" name="idSubsidio" id="idSubsidio" placeholder="">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="inputPassword5">Associar  imposto</label>
                                <select name="subsidio_imposto[]"  multiple class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"    data-selected-text-format="values" tabindex="-98"  autocomplete="associar_imposto" id="idImposto">
                                    @foreach ($getImposto as $element)
                                        <option value={{$element->id_imposto}}>{{$element->display_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="inputAddress">Descrição</label>
                                <input required type="text" class="form-control" name="descricaoSubsidio" id="descricaoSubsidio" placeholder="Descrição">
                            </div>                                        
                            {{-- <div class="form-group col-md-12">
                                <label for="inputPassword4">Ano de subsídio</label>
                                <input required type="month" name="yearSubsidio" class="form-control" id="yearSubsidio" >
                            </div> --}}
                            <button type="submit" class="btn btn-success">Gravar</button>
                        </div>
                    </form>  
                </div>
            </div>
        </div>
      </div>
    </div>
</div>


<div class="content-panel">
    @include('RH::index_menu');
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1>{{$action}}</h1>
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>
    <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
    <div class="content-fluid ml-4 mr-4 mb-5">
        <div class="d-flex align-items-start">
            @include('RH::index_menuConfiguracoes')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #7eaf3e; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>
                        
                        @if ($section=="created_imposto")
                            <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">Criar subsídios</h5>
                            {{-- formularios --}}
                            <div class="col-12 mb-4 border-bottom">
                                <form method="POST" action="{{ route('create.subsidioRH') }}" class="pb-4">
                                    @csrf

                                    <div class="form-row">

                                        <div class="form-group col-md-6">
                                            <label for="inputEmail4">Nome do subsídio</label>
                                            <input required type="text" class="form-control" name="nameSubsidio" id="nameSubsidio" placeholder="Digite o nome do subsídio Exp.: SS">
                                        </div>
                                        {{-- <div class="form-group col-md-6">
                                            <label for="inputPassword5">Associar imposto</label>
                                            <select name="listmonth[]"  multiple class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"    data-selected-text-format="values"  tabindex="-98"  autocomplete="associar_imposto" id="imposto">
                                                @foreach ($getImposto as $element)
                                                    <option value={{$element->id_imposto}}>{{$element->display_name}}</option>
                                                @endforeach
                                            </select>
                                        </div> --}}
                                        <div class="form-group col-md-6">
                                            <label for="inputAddress">Descrição</label>
                                            <input required type="text" class="form-control" name="descricaoSubsidio" id="inputAddress" placeholder="Descrição">
                                        </div>                                        
                                        {{-- <div class="form-group col-md-6">
                                            <label for="inputPassword4">Ano de subsídio</label>
                                            <input required type="month" name="yearSubsidio" class="form-control" id="year" >
                                        </div> --}}

                                    </div>

                                    <button data-toggle="modal" data-target="#staticBackdrop" type="submit" class="btn btn-primary">Gravar</button>
                                </form>
                            </div>

                            <div class="container-fluid ml-2  mr-2 mt-3">
                                <table id="users-imposto"  class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Subsídio de trabalho</th>
                                            <th>Descrição</th>
                                            <th>Estado</th>
                                            <th>Imposto associados</th>
                                            <th>Criado por</th>
                                            <th>Criado aos</th>
                                            <th>Acções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>                            
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
   
</div>
<br><br><br>
@endsection
@section('scripts')
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script>
        var id_imposto='{{$id_imposto}}'
        var idImpostoCopy=$("#idImpostoCopy")
        var selectYearImposto=$("#selectYearImposto")

        var getyearImpostoData = ""; //@json($getyearImposto);
        var requests = JSON.parse(JSON.stringify(getyearImpostoData));
       
        

        $('#users-imposto').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('recurso.ajaxSubsidio') !!}',
                columns: [
                    {
                        data: 'DT_RowIndex', 
                        orderable: false, 
                        searchable: false
                    },{
                        data: 'display_name',
                        name: 'display_name'
                    },{
                        data: 'descricao',
                        name: 'descricao'
                    },{
                        data: 'status',
                        name: 'status'
                    },                      
                    {
                        data: 'imposto',
                        name: 'imposto',
                        className: "carla",
                        orderable: false,
                        searchable: false
                    },{
                        data: 'created_by',
                        name: 'created_by'
                    },{
                        data: 'created_at', 
                        name: 'created_at'
                    },{
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    } 
                ],
                    columnDefs: [{
                    targets: 3,
                        render: function ( data, type, row ) {
                            if (data === 'panding'){
                                return '<span class="bg-info p-1">não utilizado</span>';             
                            } else {
                                return '<span class="bg-success p-1 text-white">Utilizado</span>';
                            }
                        }
                    }],
                "lengthMenu": [ [10, 50, 100, 50000],  [10, 50, 100, "Todos"]
                    ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                },
        });
</script>
@endsection