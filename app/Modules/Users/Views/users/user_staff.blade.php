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

<div class="content-panel">
    @include('RH::index_menu')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1>GESTÃO DO STAFF</h1>
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>

    <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
    
    <div class="content-fluid ml-4 mr-4 mb-5">
        <div class="d-flex align-items-start">
            @include('RH::index_menuStaff')
            
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col"
                id="v-pills-tabContent">

                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-4 "></div>
                        
                        <h5 class="col-md-12 mb-4 text-right text-muted text-uppercase">
                            <i class="fas fa-user-plus"></i> 
                            STAFF
                        </h5>
                        
                        {{-- formularios --}}
                        <div class="col-12 mb-4 border-bottom">
                            {{-- <div class="content">
                                <div class="container-fluid"> --}}
                                    
                                    {{-- <div class="row"> --}}
                                        
                                        <div class="col">

                                            @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn','rh_chefe']))
                                            <a href="{{ route('users.rpa') }}" class="btn btn-success ml-4 mt-3" style="width:200px">
                                            @icon('fa-solid fa-file-pdf') Gerar Registo Primário
                                            </a>
                                            @endif
                    
                                            @if(auth()->user()->hasAnyPermission(['manage-users']))
                                                <a href="{{ route('users.create_user_staff') }}" class="btn btn-primary ml-4 mt-3" style="width:200px">
                                                    @icon('fas fa-plus-square')
                                                    Criar novo staff
                                                </a>
                                            @endif
                                                {{-- <div class="float-right mr-4" style="width:200px; !important">
                                                        <select name="curso" id="curso" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
                                                           
                                                            @foreach ($curso_model as $item_curso)
                                                               
                                                                    <option value="{{ $item_curso->id }}" selected>
                                                                     {{ $item_curso->nome_curso }}
                                                                    </option> 
                                                            @endforeach 
                                                        </select>
                                                    </div>  --}}
                                            <div class="card">
                                                <div class="card-body">
                    
                                                    <table id="users-table" class="table table-striped table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Matrícula</th>
                                                            <th>Nome do funcionário</th>
                                                            <th>@lang('Users::users.email')</th>
                                                            <th>Nº bi</th>
                                                            <th>Telefone</th>
                                                            <th>@lang('Users::roles.roles')</th>
                                                            <th>@lang('common.created_by')</th>
                                                            <th>@lang('common.updated_by')</th>
                                                            <th>@lang('common.created_at')</th>
                                                            <th>@lang('common.updated_at')</th>
                                                            {{-- <th>Estado</th> --}}
                                                            {{-- <th>Entidade bolseira</th> --}}
                                                            <th>@lang('common.actions')</th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                    
                                                </div>
                                            </div>
                                        </div>

                                    {{-- </div> --}}
                                
                                {{-- </div>
                            </div> --}}
                            
                            {{-- modal confirm --}}
                            @include('layouts.backoffice.modal_confirm') 
                            
                            {{-- MODAL DE CONFIRMAÇÃO DE ELIMINAÇÃO  CODIGO ACRESCENTADO NÃO ESTAVA AQUI, CASO VENHA USAR COMENTAR A LINHA DE CIMA --}}
                            {{-- <div class="modal fade" id="modal_confirm">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">@lang('modal.confirm_title')</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>
                                                <span>@lang('modal.confirm_text')</span>&nbsp;<span class="modal-confirm-text"></span>
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <form method="POST" action="/users/users" accept-charset="UTF-8" class="d-inline">
                                                <input name="_method" type="hidden" value="">
                                                <input name="_token" type="hidden" value="">
                                                <button type="submit" class="btn forlearn-btn" id="delete-btn">
                                                    <i class="far fa-check-square"></i>@lang('modal.confirm_button')
                                                </button>
                                                <button type="button" class="btn forlearn-btn" data-dismiss="modal">
                                                    <i class="far fa-window-close"></i>@lang('modal.cancel_button')
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        
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
    <script>

        var curso=$("#curso");
    
        
        $(function () {
            $('#users-table').DataTable({
                ajax: '{!! route('users.getStaff') !!}',
                columns: [
                    {
                    data: 'DT_RowIndex',
                    orderable: false, 
                    searchable: false
                  },

                  {
                    data: 'matricula',
                    name: 'up_meca.value',
                    visible: true,
                    //orderable: true,
                  },
                    {
                    data: 'name',
                    name: 'name',
                    visible: true,
                    //orderable: true,
                  }, {
                    data: 'email',
                    name: 'email',
                    searchable: true,
                    //orderable: true,
                },
                {
                    data: 'bilhete',
                    name: 'up_bi.value',
                    searchable: true,
                    orderable: true,
                },
                {
                    data: 'telefone',
                    name: 'up_phone.value',
                    searchable: true,
                    orderable: true,
                },{
                    data: 'roles',
                    name: 'roles',
                    searchable: true,
                    orderable: true,
                }, 
                {
                    data: 'created_by',
                    name: 'u1.name',
                    visible: false
                }, {
                    data: 'updated_by',
                    name: 'u2.name',
                    visible: false
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    visible: false
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    visible: false
                }, 
                // {
                //     data: 'states',
                //     name: 'states',
                // },

                // {
                //     data: 'scholarship-entity',
                //     name: 'scholarship-entity',
                // },
                
                {
                    data: 'actions',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
                ],
                "lengthMenu": [ [10, 50, 100, 50000], [10, 50, 100, "Todos"] ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');


    </script>
@endsection
