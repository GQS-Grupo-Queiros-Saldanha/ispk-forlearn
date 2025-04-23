<title>Recursos humanos | forLEARN® by GQS</title>
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
    .fotoUserFunc{
        border-radius: 50%;
        background-color: #c4c4c4;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: 50%;
        width: 150px;
        height: 150px;
        -webkit-filter: brightness(.9);
        filter: brightness(.9);
        border: 5px solid #fff;
        -webkit-transition: all .5s ease-in-out;
        transition: all .5s ease-in-out;
    }
</style>

<div class="content-panel">

    
    <div class="content-header">
        @include('RH::index_menu')
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1>{{$data['action']}}</h1>
                   
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>
    <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
    <div class="content-fluid ml-4 mr-4 mb-0">
        <div class="d-flex align-items-start">
            @include('RH::index_menuStaff')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-2"></div>
                        <div class="row col-md-12  ">
                            <div class="form-group col-md-6 mr-0 pr-0">
                                <p class="text-muted">Informações</p>
                                <div class="row col-md-12 m-0 p-0 border-top">
                                    <div hidden style="display: flex; align-items: center;" class="col-md-7 border-right">
                                        <table>
                                            <thead>
                                                <input type="hidden" id="qdt-contrato-fun" value="0">
                                                <tr><td><i class="fa fa-users" aria-hidden="true"></i> Funcionários(as) com contrato: <small style="font-size: 1.1pc"><b id="fun-contrado"></b></small></td></tr>
                                                <tr><td><i class="fa fa-f" aria-hidden="true"></i> Funcionários(as) sem contrato: <samp><b id="fun-sem-contrado"></b></samp>  </td></tr>
                                                {{-- <tr><td><i class="fa fa-calendar-check" aria-hidden="true"></i> Data início do contrato: <samp><b class="data-inicio-contrato"></b></samp>  </td></tr>
                                                <tr><td><i class="fa fa-calendar-xmark" aria-hidden="true"></i> Data fim do contrato: <samp><b class="data-fim-contrato"></b></samp>  </td></tr>
                                                <tr><td><i class="fa fa-user" aria-hidden="true"></i> Criado por: <small class="criado-por text-muted"></small>  </td></tr> --}}
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mr-0 pl-0">
                                <h6 class="col-md-12 mb-0 text-right text-muted text-uppercase ml-0 pl-0 mr-0 pl-0"><i class="fas fa-user-group"></i> Lista dos fun. com contrato e os sem contrato</h6>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid ml-2  mr-2 mt-3">
                        <table id="users-table"  class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Cargo(s) com contrato</th>
                                    {{-- <th>Criado por</th> --}}
                                    {{-- <th>Acções</th> --}}
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

@endsection
@section('scripts')
    @parent
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> --}}
    <script>
        var getRoles=$("#roles");
        var ctx = document.getElementById("myChart");
  
        $(function () {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('recurso_humano.ajax_listagem') !!}',
                buttons: [
                    'colvis',
                    'excel'
                ],
                columns: [
                {
                    data: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false
                },{
                    data: 'nome_usuario',
                    name: 'nome_usuario'
                },{
                    data: 'email',
                    name: 'email'
                }, {
                    data: 'roles',
                    name: 'roles'
                }
                // , {
                //     data: 'created_by',
                //     name: 'created_by'
                // }
                // , {
                //     data: 'created_at', 
                //     name: 'created_a'
                // }, 
                // {
                //     data: 'actions', 
                //     name: 'actions'
                // }

                ],
                "lengthMenu": [ [10, 50, 100, 50000],  [10, 50, 100, "Todos"]
                    ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                },
            });

          

        })
        // Delete confirmation modal criar
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        console.log("AA");
    </script>
@endsection
