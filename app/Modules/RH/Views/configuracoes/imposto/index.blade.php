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

<!-- Modal que elimina a taxa   -->
<div class="modal fade" id="delete_imposto" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Informação!</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Caro utilizador deseja eliminar esta?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <form id="formRoute_delete-imposto" method="POST" action="">
            @csrf
              <input type="hidden" name="getId" id="getId">
            <button type="submit" class="btn btn-primary">Ok</button>
          </form>
        </div>
      </div>
    </div>
</div>

<!-- Modal para editar imposto  -->
<div class="modal fade" id="editar_imposto" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-xl" role="document">
      <div class="modal-content" style="z-index: 99999;border-top-left-radius: 10px;border-top-right-radius: 10px ">
        <div style="background:#7eaf3e;width: 100%;border-top-left-radius: 15px;border-top-right-radius: 15px;height: 5px;" class="m-0" ></div>
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Editar</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div>
        <div class="modal-body">
            <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                <div class="col-12 mb-4 ">
                    <form id="formRoute-Edita-imposto" method="POST" action="" class="pb-4">
                        @csrf
                        <div hidden id="editarImposto">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="inputEmail4">Nome do imposto</label>
                                    <input required type="text" class="form-control" name="nameImposto" id="nameImposto" placeholder="Digite o nome do imposto Exp.: IRT">
                                    <input  type="hidden" class="form-control" name="idImposto" id="idImposto" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputAddress">Descrição</label>
                                <input required type="text" class="form-control" name="descricaoImposto" id="descricaoImposto" placeholder="Descrição">
                            </div>
                            <button type="submit" class="btn btn-success">Gravar</button>
                        </div>
                    </form>
                    <form id="formRoute-Edita-impostoYear" method="POST" action="" class="pb-4">
                        @csrf
                        <div hidden  id="editarImpostoYear">
                            <div class="form-group col-md-9 ml-0 pl-0">
                                <label for="inputPassword4">Ano de imposto</label>
                                <input required type="month" name="yearImposto" class="form-control" id="year" >
                                <input  type="hidden" name="idyearImposto" id="idyearImposto" class="form-control" >
                            </div>
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
    @include('RH::index_menu')
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
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col"
                id="v-pills-tabContent">

                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #7eaf3e; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>
                        @if ($section=="created_imposto")
                            <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">Criar imposto</h5>
                            {{-- formularios --}}
                            <div class="col-12 mb-4 border-bottom">
                                <form method="POST" action="{{ route('create.impostoRH') }}" class="pb-4">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label for="inputEmail4">Nome do imposto</label>
                                            <input required type="text" class="form-control" name="nameImposto" id="nameImposto" placeholder="Digite o nome do imposto Exp.: IRT">
                                        </div>
                                        {{-- <div class="form-group col-md-6">
                                            <label for="inputPassword4">Ano de imposto</label>
                                            <input required type="month" name="yearImposto" class="form-control" id="year" >
                                        </div> --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="inputAddress">Descrição</label>
                                        <input required type="text" class="form-control" name="descricaoImposto" id="inputAddress" placeholder="Descrição">
                                    </div>
                                    <button data-toggle="modal" data-target="#staticBackdrop" type="submit" class="btn btn-primary">Gravar</button>
                                </form>
                            </div>

                            <div class="container-fluid ml-2  mr-2 mt-3">
                                <table id="users-imposto"  class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nome</th>
                                            <th>Descrição</th>
                                            <th>Estado do Imposto</th>
                                            <th>Anos</th>
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
                        @if ($section=="created_yearImposto")
                        <a href="{{ route('config.recurso_humanoImposto') }}" class="ml-4"><i style="font-size: 1.3pc" class="fas fa-chevron-left" aria-hidden="true"></i></a>
                            <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">Criar ano para imposto</h5>
                            {{-- formularios --}}
                            <div class="col-12 row mt-4 mr-0 pr-0">
                                <div class="col-md-3 border-right">
                                    <form method="POST" action="{{ route('recurso.createYearImposto') }}" class="pb-4">
                                        @csrf
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="inputEmail4">Nome do imposto</label>
                                                <input readonly style="color: #979797;font-weight: bold" type="text" value="{{$getyearImposto[0]->name_imposto}}" class="form-control" name="nameImposto" id="nameImposto" placeholder="Digite o nome do imposto Exp.: IRT">
                                                <input  type="hidden" value="{{$id_imposto}}" class="form-control" name="id_imposto">
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="inputPassword4">Ano de imposto</label>
                                                <input required type="month" name="yearImposto" class="form-control" id="year" >
                                            </div>
                                        </div>
                                        <button data-toggle="modal" data-target="#staticBackdrop" type="submit" class="btn btn-primary">Gravar</button>
                                    </form>
                                </div>
                                <div class="col-md-9 mr-0 pr-0">
                                    <div class="mt-5 mr-0 pr-0">
                                        <table id="users-impostoYear"  class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nome</th>
                                                    <th>Descrição</th>
                                                    <th>Estado do Imposto</th>
                                                    <th>Ano-mes</th>
                                                    <th>Imposto em vigor</th>
                                                    <th>Criado aos</th>
                                                    <th>Acções</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>  
                            </div>   
                        @endif
                        <div hidden id="formCopyImposto" class="col-md-12 pr-3 pl-3 ">
                            <div style="background: #eff3f5"
                                class="container jumbotron col-md-12 mt-4 mb-0 pb-3 rounded">
                                <h1 class="ml-0 pl-0">OLA, {{Auth::user()->name}}!</h1>
                                {{-- <p class="lead"> IRT <b>2021(Janeiro)</b></p> --}}
                                <hr class="my-4">
                                <p>Por favor seleciona o ano (Ex.: 20..[...]) que pentende aplicar a mesma taxa/s <b id="copyYear"> </b> escolhida </p>
                                <form method="post" action="{{route('recurso.impostoYearCopy') }}">
                                    @csrf
                                    <input type="hidden" id="idImpostoCopy" name="idImpostoCopy">
                                    <div class="col-md-4 pl-0 ml-0 mb-4 mt-4">
                                        <label for="inputEmail4">Ano do imposto</label>
                                        <select class="selectpicker form-control form-control-sm" name="selectYearImposto" id="selectYearImposto">
                                        </select>
                                    </div>
                                    <p class="lead">
                                        <button type="submit" class="btn btn-success" >Gravar <i class="fas fa-copy"></i></button>
                                    </p>
                                </form> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script>
        var id_imposto='{{$id_imposto}}'
        var idImpostoCopy=$("#idImpostoCopy")
        var selectYearImposto=$("#selectYearImposto")

        var getyearImpostoData = @json($getyearImposto);
        var requests = JSON.parse(JSON.stringify(getyearImpostoData));
       
        

        $('#users-imposto').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('recurso.ajaxImposto') !!}',
                columns: [
                    {
                        data: 'DT_RowIndex', 
                        orderable: false, 
                        searchable: false
                    },{
                        data: 'display_name',
                        name: 'impost.display_name'
                    },{
                        data: 'descricao',
                        name: 'descricao'
                    },{
                        data: 'status',
                        name: 'impost.status'
                    }, {
                        data: 'years',
                        name: 'year',
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
                                return '<span class="bg-info p-1">Pendente</span>';             
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
        $('#users-impostoYear').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: 'configuracoes-ajaxYearImposto/'+id_imposto,
                    columns: [
                    {
                        data: 'DT_RowIndex', 
                        orderable: false, 
                        searchable: false
                    },{
                        data: 'display_name',
                        name: 'impost.display_name'
                    },{
                        data: 'descricao',
                        name: 'descricao'
                    },{
                        data: 'status',
                        name: 'impost_year.status'
                    },{
                        data: 'year_months',
                        name: 'year_months'
                    }
                    ,{
                        data: 'estado',
                        name: 'impost_year.estado'
                    }
                    ,{
                        data: 'created_at', 
                        name: 'impost_year.created_at'
                    },{
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                      } 
                    ],
                    columnDefs: [{
                        targets: [3,5],
                            render: function ( data, type, row,meta ) {
                                if (meta.col==3) {
                                    if (data === 'panding'){
                                        return '<span class="bg-info p-1">não utilizado no sistema</span>';             
                                    } else {
                                        return '<span class="bg-success p-1 text-white">Já utilizado no sistema</span>';
                                    }
                                }else if(meta.col==5){
                                    if (data === 0){
                                        return '<input  type="checkbox" disabled>';             
                                    } else {
                                        return '<input  type="checkbox"  checked disabled>';
                                    }
                                } 
                            }
                    }],
                    
                    "lengthMenu": [ [10, 50, 100, 50000],  [10, 50, 100, "Todos"]
                        ],
                    language: {
                        url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                    },    
        });
        
        function get_impostoData() {
            selectYearImposto.empty();
            selectYearImposto.append('<option selected="" value=""></option>');
            $.each(requests, function (index, item) { 
                if (idImpostoCopy.val()!=item.id_impostoYear) {
                    selectYearImposto.append('<option value="' +item.id_impostoYear+ '">' +item.year_month+ '</option>')
                }
            });
            selectYearImposto.selectpicker('refresh');
        }
       
</script>
@endsection