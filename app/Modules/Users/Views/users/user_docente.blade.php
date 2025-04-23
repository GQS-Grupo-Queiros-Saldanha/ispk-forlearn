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
    {{-- <div style="z-index: 1900" class="modal fade modal_loader" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> 
            <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
        </div>
    </div> --}}

<div class="content-panel" >
    
    @include('RH::index_menu')

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        {{-- <h1>@lang('Users::users.users')</h1> --}}
                        <h1>GESTÃO DO STAFF</h1>
                    </div>
                    {{-- <div class="col-sm-6">
                        {{ Breadcrumbs::render('users') }}
                    </div> --}}
                </div>
            </div>
        </div>


    <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
    <div class="content-fluid ml-4 mr-4 mb-5">
        <div class="d-flex align-items-start">
            
            @include('RH::index_menuStaff')
            
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>
                        
                            <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">DOCENTES</h5>
                            {{-- formularios --}}
                            <div class="col-12 mb-4 border-bottom">


                                {{-- Main content --}}
                                <div class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col p-0">

                                                @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']) || auth()->user()->hasAnyPermission(['criar_docente']))
                                                    <a href="{{ route('users.create_user_docente') }}" class="btn btn-success ml-0  mt-3" style="width:200px">
                                                        @icon('fas fa-plus-square')
                                                        Criar novo docente
                                                    </a>
                                                @endif
                                                
                                                 <a href="{{ route('users.generate.docente.pdf') }}" class="btn btn-info ml-1 mt-3" target="_blank"
                        style="width:150px;">
                        @icon('fas fa-file-pdf-o')
                        Gerar PDF
                      </a>
                                                    {{-- <div class="float-right mr-4" style="width:200px; !important">
                                                            <select name="curso" id="curso" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
                                                            
                                                            </select>
                                                        </div>  --}}
                                                
                                                <div class="float-right mr-0 mb-0 pr-0" style="width:470px; !important">                            
                                                    <label>Selecione o curso</label> 
                                                    <br>
                                                    <select name="curso" id="curso" class="selectpicker form-control form-control-sm" data-live-search="true" style="width: 100%; !important">
                                                        
                                                        {{-- @foreach ($curso_model as $item_curso)
                                                            
                                                                <option value="{{ $item_curso->id }}" selected>
                                                                    {{ $item_curso->nome_curso }}
                                                                </option> 
                                                        @endforeach  --}}
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    
                                                </div>  
                                            </div>
                                            
                                            <div class="card col-12 mt-5">
                                                <br><br><br>
                                                <div class="card-body">

                                                    <table id="users-table" class="table table-striped table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Matrícula</th>
                                                            <th>Nome do docente</th>
                                                            <th>@lang('Users::users.email')</th>
                                                            {{-- <th>Curso</th> --}}
                                                            {{-- <th>@lang('Users::roles.roles')</th> --}}
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

                        
    </div>

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts')
    @parent
    <script>

        var curso=$("#curso");
        
        getCurso();
        function getCurso () {  
            console.log("dsd");
            $.ajax({
                url: "getCurso",
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function (data){
                console.log(data);
                curso.empty();     
                curso.append('<option selected="" value="0">Selecione o curso</option>');

                if (data['data'].length>0) {
                    $.each(data['data'], function (indexInArray, row) { 
                        curso.append('<option value="'+ row.id+'">'  + row.nome_curso + '</option>');
                    });
                }

                curso.prop('disabled', false);
                curso.selectpicker('refresh');
                                                        
            });
        }



        $(function () {
            $('#users-table').DataTable({
                ajax: '{!! route('users.getDocente') !!}',
                buttons:['colvis','excel'],
                columns: [
                    {
                    data: 'DT_RowIndex',
                    orderable: false, 
                    searchable: false
                
                  },

                  {
                    data: 'matricula',
                    name: 'up_meca.value',
                    visible: true
                  },
                    {                    
                    data: 'nome_student',
                    name: 'full_name.value',
                    visible: true
                  }, {
                    data: 'email',
                    name: 'email'
                },
                // {
                //     data: 'course',
                //     name: 'ct.display_name',
                // },
                // {
                //     data: 'roles',
                //     name: 'roles'
                //     // searchable: true
                // }, 
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

        



        curso.bind('change keypress', function() {
            $('#users-table').DataTable().clear().destroy();
            var id_curso=curso.val();
            var v= $('#users-table').DataTable({
                            ajax: {
                            url:"getDocenteCourse/"+id_curso,
                            "type": "GET"
                            },
                            buttons:[
                                'colvis',
                                'excel'
                            ],

                            columns: [ 
                            {
                                data: 'DT_RowIndex',
                                orderable: false, 
                                searchable: false
                           
                            },
                        
                            {
                                data: 'matricula',
                                name: 'up_meca.value',
                                visible: true

                            }
                            , {                                
                                data: 'nome_student',
                                name: 'full_name.value',
                                visible: true

                            }
                            
                            ,{
                                data: 'email',
                                name: 'email'

                            },
                            // {
                            //     data: 'course',
                            //     name: 'ct.display_name',
                            // },
                          
                           {
                                data: 'created_by',
                                name: 'u1.name',
                                visible: false

                            },
                            {
                                data: 'updated_by',
                                name: 'u2.name',
                                visible: false

                            },
                            {
                                data: 'created_at',
                                name: 'created_at',
                                visible: false
                            },{
                                data: 'updated_at',
                                name: 'updated_at',
                                visible: false

                            }
                          
                            ,{
                                data: 'actions',
                                name: 'action',  
                                visible: false,
                                // orderable: false,
                                searchable: false
                             }
                            ],
                                "lengthMenu": [ [10, 50, 100, 50000], [10, 50, 100, "Todos"] ],
                                language: {
                                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                                }
                        });    

                        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

                console.log(id_curso);
            $.ajax({
                url:"getDocenteCourse/"+id_curso,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function (data){
                console.log(data);
                                                        
            });

        })

    </script>
@endsection
