@section('title',__('Docentes | forLEARN® by GQS'))
@extends('layouts.generic_index_new')
@section('page-title', 'GESTÃO DE DOCENTES')
@section('styles')
@parent
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Docentes</li>
@endsection
@section('selects')
<div class="float-right mr-0 mb-0 pr-0" style="width:470px; !important">                            
                                                    <label>Selecione o curso</label> 
                                                    <br>
                                                    <select name="curso" id="curso" class="selectpicker form-control form-control-sm" data-live-search="true" style="width: 100%; !important">
                                                        
                                                      
                                                    </select>
                                                </div>
@endsection
@section('body')
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


    @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']) || auth()->user()->hasAnyPermission(['criar_docente']))
     <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
    @endif 
    <div class="content-fluid ml-4 mr-4 mb-5">
        <div class="d-flex align-items-start">
        @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']) || auth()->user()->hasAnyPermission(['criar_docente']))
            @include('RH::index_menuStaff')
         @endif   
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        
                        
                           
                            {{-- formularios --}}
                            <div class="col-12 mb-4 border-bottom">


                                {{-- Main content --}}
                                <div class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col p-0">
                                            
                                            <div class="card col-12 mt-5">
                                           
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
                buttons:['colvis','excel'
                @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']) || auth()->user()->hasAnyPermission(['criar_docente']))
                , {
                            text: '<i class="fas fa-plus-square" ></i> Criar novo docente',
                            className: 'btn btn-success ml-1 rounded',
                            action: function(e, dt, node, config) {
                                let url = "{{ route('users.create_user_docente') }}";
                                window.open(url, "_blank");
                            }
                        }
                @endif 
                , {
                            text: '<i class="fas fa-file-pdf " ></i> Gerar PDF',
                            className: 'btn btn-info ml-1 rounded',
                            action: function(e, dt, node, config) {
                                let url = "{{ route('users.generate.docente.pdf') }}";
                                window.open(url, "_blank");
                            }
                        } 
                    
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
