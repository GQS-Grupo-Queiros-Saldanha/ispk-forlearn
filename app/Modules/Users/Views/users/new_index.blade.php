<title>Utilizadores  | forLEARN® by GQS</title>
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')
    <div class="content-panel" style="padding:0px">
         @include('Users::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        {{-- <h1>@lang('Users::users.users')</h1> --}}
                        <h1>ESTUDANTES</h1>
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

                        @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']) || auth()->user()->hasAnyPermission(['gerir_matrículas']) )
                            <a href="{{ route('users.create_user') }}" class="btn btn-primary ml-4 mt-3" style="width:200px">
                                @icon('fas fa-plus-square')
                                Criar novo estudante
                            </a>
                        @endif
                          <a href="{{ route('users.generate.pdf', ['id_curso' => '']) }}" class="btn btn-info ml-1 mt-3"
                        target="_blank" style="width:150px" id="generate-pdf-link">
                        @icon('fas fa-file-pdf-o')
                        Gerar PDF
                    </a>

                        
                        <div class="float-right mr-4" style="width:470px; !important">                            
                            <label>Selecione o curso</label> 
                            <select name="curso" id="curso" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
                                
                                {{-- @foreach ($curso_model as $item_curso)
                                    
                                        <option value="{{ $item_curso->id }}" selected>
                                            {{ $item_curso->nome_curso }}
                                        </option> 
                                @endforeach  --}}
                            </select>
                        </div> 
                                                  
                        <div class="form-group">
                            
                        </div>                         

                        <div class="card">
                            <div class="card-body">

                                <table id="users-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Matrícula</th>
                                        <th>Nome do estudante</th>
                                        <th>@lang('Users::users.email')</th>
                                        <th>Nº BI</th> 
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

$(document).ready(function () {
        var link = $('#generate-pdf-link'); // Seletor do link para o PDF

        // Esconde o botão inicialmente
        link.hide();

        // Atualiza o link do PDF quando o curso selecionado muda
        $('#curso').on('change', function () {
            var selectedCurso = $(this).val(); // Pega o ID do curso selecionado

            if (selectedCurso && selectedCurso !== "0") {
                // Substitui o ID do curso no URL do PDF
                var url = '{{ route('users.generate.pdf', ['id_curso' => '__CURSO_ID__']) }}';
                url = url.replace('__CURSO_ID__', selectedCurso);

                link.attr('href', url); // Atualiza o atributo href do link
                link.show(); // Mostra o botão
            } else {
                link.hide(); // Esconde o botão se nenhum curso for selecionado
            }
        });

        // Adiciona evento de clique ao botão de gerar PDF
        link.on('click', function (e) {
            var selectedCurso = $('#curso').val(); // Pega o ID do curso selecionado

            if (!selectedCurso || selectedCurso === "0") {
                e.preventDefault(); // Impede o redirecionamento
                Toastr.error("Selecione um curso");
            }
        });
    });

        $(function () {
            $('#users-table').DataTable({
                ajax: '{!! route('users.ajax') !!}',
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
                {
                    data: 'n_bi',
                    name: 'up_bi.value'
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
                            url:"getStudent/"+id_curso,
                            "type": "GET"
                            },
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
                            {
                                data: 'n_bi',
                                name: 'up_bi.value'
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
                url:"getStudent/"+id_curso,
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
