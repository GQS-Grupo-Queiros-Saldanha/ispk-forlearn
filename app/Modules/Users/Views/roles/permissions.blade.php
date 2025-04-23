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
    #permissions-table_filter{
        display:none!important;
    }
</style>

    <!-- Modal  que apresenta a loande do  site -->
    <div style="z-index: 1900" class="modal fade modal_loader" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> 
            <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
        </div>
    </div>

@section('content')
    
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
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('Users::roles.permissions')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('roles.permissions', $role) }} 
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
                            
                                <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">PERMISSÕES</h5>
                                {{-- formularios --}}
                                <div class="col-12 mb-4 border-bottom">
                                    
                                    {{-- Main content --}}
                                    <div class="content">
                                        <div class="container-fluid">

                                            {!! Form::open(['route' => ['roles.savePermissions',$role->id], 'method' => 'put']) !!}

                                            <div class="row">
                                                <div class="col">

                                                    <button type="submit" class="btn btn-success mb-3">
                                                        @icon('fas fa-save')
                                                        @lang('common.save')
                                                    </button>

                                                    <div class="card">
                                                        <div class="card-body">

                                                            <table id="permissions-table" class="table table-striped table-hover">
                                                                <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th>@lang('Users::roles.name')</th>
                                                                    <th>@lang('translations.display_name')</th>
                                                                    <th>@lang('common.created_at')</th>
                                                                    <th>@lang('common.updated_at')</th>
                                                                </tr>
                                                                </thead>
                                                            </table>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {!! Form::close() !!}

                                        </div>
                                    </div>
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
    <script>
        $(function () {
            $('#permissions-table').DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                ajax: '{!! route('roles.permissions.ajax', $role->id) !!}',
                columns: [{
                    data: 'select',
                    name: 'select',
                    className: 'text-center adjust-checkbox-margin-top',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'display_name',
                    name: 'pt.display_name'
                }, {
                    data: 'created_at',
                    name: 'created_at'
                }, {
                    data: 'updated_at',
                    name: 'updated_at'
                }],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });
    </script>
@endsection
