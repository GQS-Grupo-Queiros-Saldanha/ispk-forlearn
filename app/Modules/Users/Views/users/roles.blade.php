
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
                                @lang('Users::roles.roles')
                            </h5>
                        
                            {{-- formularios --}}
                            <div class="col-12 mb-4 border-bottom">

                                {{-- Main content --}}
                                <div class="content">
                                    <div class="container-fluid">

                                        {!! Form::open(['route' => ['users.saveRoles', $user->id], 'method' => 'put']) !!}

                                        <div class="row">
                                            <div class="col">

                                                <button type="submit" class="btn btn-sm btn-success mb-3">
                                                    @icon('fas fa-save')
                                                    @lang('common.save')
                                                </button>

                                                <div class="card">
                                                    <div class="card-body">

                                                        <table id="roles-table" class="table table-striped table-hover">
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
            $('#roles-table').DataTable({
                paging: false,
                searching: false,
                ajax: '{!! route('users.roles.ajax', $user->id) !!}',
                columns: [{
                    data: 'select',
                    name: 'select',
                    className: 'text-center adjust-checkbox-margin-top',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'name',
                    name: 'name',
                    visible: false
                }, {
                    data: 'display_name',
                    name: 'rt.display_name'
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    visible: false
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    visible: false
                }],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });
    </script>
@endsection
