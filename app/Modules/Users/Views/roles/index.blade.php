@section('title',__('RH - cargos'))
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
                            @lang('Users::roles.roles')
                            <br>
                            <span class="text-muted">Perfis de utilizadores que podem executar acções, <br> 1 utilizador tem N roles,<br> ex: Administrador, Funcionário, Docente</span>
                        </h5>


                        {{-- Main content --}}
                        {{-- <div class="col-12"> --}}
                            <div class="col" style="background-color: #f5fcff">

                                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm mb-3">
                                    @icon('fas fa-plus-square')
                                    @lang('common.new')
                                </a>

                                <table id="roles-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('Users::roles.name')</th>
                                        <th>@lang('translations.display_name')</th>
                                        <th>@lang('Users::roles.guard_name')</th>
                                        <th>@lang('common.created_by')</th>
                                        <th>@lang('common.updated_by')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
                                        <th>@lang('common.actions')</th>
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
            $('#roles-table').DataTable({
                ajax: '{!! route('roles.ajax') !!}',
                columns: [{
                    data: 'name',
                    name: 'name',
                    visible: false
                }, {
                    data: 'display_name',
                    name: 'rt.display_name'
                }, {
                    data: 'guard_name',
                    name: 'guard_name',
                    visible: false
                }, {
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