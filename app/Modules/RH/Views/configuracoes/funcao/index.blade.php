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
                                <label for="inputEmail4">Nome</label>
                                <input required type="text" class="form-control" name="display_name" id="display_name" placeholder="Digite o nome da função">
                                <input  type="hidden" class="form-control" name="idSubsidio" id="idSubsidio" placeholder="">
                            </div>
                            
                            <div class="form-group col-md-12">
                                <label for="inputAddress">Descrição</label>
                                <input required type="text" class="form-control" name="descricao" id="descricao" placeholder="Descrição">
                            </div>

                            {{-- <button type="submit" class="btn btn-success">Gravar</button> --}}
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                @icon('fas fa-save')
                                @lang('common.save')
                            </button>
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
                    <h1>CONFIGURAÇÕES RH</h1>
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>



    <div class="content-fluid ml-4 mr-4 mb-5">
        <div class="d-flex align-items-start">
            @include('RH::index_menuConfiguracoes')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col-md-10"
                id="v-pills-tabContent">

                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #7eaf3e; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>

                        <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">
                            Criar Função
                        </h5>


                        {{-- Main content --}}
                        {{-- <div class="col-12"> --}}
                            <div class="col" style="background-color: #f5fcff">

                                <a href="{{ route('recurso.Createfuncao') }}" class="btn btn-primary btn-sm mb-3">
                                    @icon('fas fa-plus-square')
                                    @lang('common.new')
                                </a>

                                <table id="funcao-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        {{-- <th>#</th> --}}
                                        <th>Nome</th>
                                        <th>Descrição</th>
                                        <th>Criado Por</th>
                                        <th>Criado aos</th>
                                        <th>Actividades</th>

                                        {{-- <th>@lang('common.code')</th>
                                        <th>@lang('translations.descricao')</th>
                                        <th>@lang('translations.display_name')</th> --}}
                                        {{-- <th>@lang('common.created_by')</th> --}}
                                        {{-- <th>@lang('common.updated_by')</th> --}}
                                        {{-- <th>@lang('common.created_at')</th> --}}
                                        {{-- <th>@lang('common.updated_at')</th>
                                        <th>@lang('common.actions')</th> --}}
                                    </tr>
                                    </thead>
                                </table>

                            </div>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>




    
   
</div>

@endsection
@section('scripts')

@parent
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> --}}

    <script>
        $(function () {
            $('#funcao-table').DataTable({
                ajax: '{!! route('recurso.ajaxFuncao') !!}',
                columns: [
                {
                    data: 'display_name',
                    name: 'display_name'
                }, {
                    data: 'descricao',
                    name: 'descricao'
                },  {
                    data: 'created_by',
                    name: 'created_by',
                }, {
                    data: 'created_at',
                    name: 'created_at',
                }, {
                    data: 'actions',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>



@endsection