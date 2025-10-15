@section('title',__('Users::users.users'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <div class="content-panel">
        
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('Users::users.users')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('users') }}
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']))
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm mb-3">
                                @icon('fas fa-plus-square') 
                                @lang('common.new')
                            </a>
                        @endif
                        <div class="card">
                            <div class="card-body">

                                <table id="users-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('Users::users.name')</th>
                                        <th>@lang('Users::users.email')</th>
                                        <th>@lang('Users::roles.roles')</th>
                                        <th>@lang('common.created_by')</th>
                                        <th>@lang('common.updated_by')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
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

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            $('#users-table').DataTable({
                ajax: '{!! route('users.ajax') !!}',
                columns: [
                    {
                    data: 'name',
                    name: 'name',
                    visible: false
                }, {
                    data: 'email',
                    name: 'email'
                },
                //---------valores da coluna cargos------------
                { 
                    data: 'roles', 
                    render: function(data) { 
                     if(data == "6") {
                            return 'Estudante' 
                        }   
                     if(data == "1") {
                            return 'Docente' 
                        }
                     if(data == "2") {
                            return 'Super administrador' 
                        }
                     if(data == "7") {
                            return 'Administrator' 
                        }
                     if(data == "8") {
                            return 'Docente > Director(a) geral' 
                        } 
                     if(data == "9") {
                            return 'Docente > Vice-director(a) área académica' 
                        }
                     if(data == "10") {
                            return 'Docente > Vice-director(a) área científica' 
                        }
                     if(data == "11") {
                            return 'Docente > Chefe de Departamento' 
                        } 
                     if(data == "12") {
                            return ' Docente > Coordenador Director de curso' 
                        }
                     if(data == "13") {
                            return 'Docente > Coordenador(a) da unidade curricular' 
                        }
                     if(data == "14") {
                            return 'Docente > Regente da unidade curricular' 
                        }
                     if(data == "15") {
                            return 'Candidato a estudante' 
                        }
                     if(data == "16") {
                            return 'Staff > Director executivo' 
                        }
                     if(data == "17") {
                            return 'Staff > Chefe de departamento' 
                        } 
                     if(data == "18") {
                            return 'Staff > Chefe de secretaria' 
                        }
                     if(data == "19") {
                            return 'Staff > Chefe de secção' 
                        }
                     if(data == "20") {
                            return 'Staff > Tesoureiro(a)' 
                        } 
                     if(data == "21") {
                            return 'Staff > Auxiliar administrativo' 
                        }
                     if(data == "22") {
                            return 'Staff > Staff > Secretário(a)' 
                        } 
                     if(data == "23") {
                            return 'Staff > Assistente administrativo' 
                        } 
                     if(data == "24") {
                            return 'Staff > Vigilantes' 
                        }
                     if(data == "25") {
                            return 'Staff > Motorista' 
                        }
                     if(data == "26") {
                            return 'Staff > Auxiliar de Higiene' 
                        }
                     if(data == "27") {
                            return 'Staff > Segurança' 
                        } 
                     if(data == "29") {
                            return 'Staff > Gestor Forearn' 
                        }
                     if(data == "41") {
                            return 'Staff > Inscrições' 
                        }
                     if(data == "42") {
                            return 'Staff > Matrículas' 
                        }
                     if(data == "43") {
                            return 'Staff > Gabinete de termos' 
                        }
                     if(data == "44") {
                            return 'Staff > Recursos humanos' 
                        } 
                     if(data == "45") {
                            return 'Staff > Chefe Tesoureiro(a)' 
                        }                                                                             
                    else {
                            return ''
                         }

                  },
                defaultContent: 'OK'
                },
                //---------TERMINA valores da coluna cargos------------
               /* {
                    data: 'roles',
                    name: 'roles'
                }, */{
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
