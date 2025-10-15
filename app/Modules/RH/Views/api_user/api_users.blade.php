@section('title', 'Criar user API')

@extends('layouts.backoffice')
<style>
    .user-profile-image {
        width: 200px !important;
    }

    input#name::placeholder {
        color: red;
    }

    input#full_name::placeholder {
        color: red;
    }

    input#id_number::placeholder {
        color: red;
    }
</style>

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row -- mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            CRIAR CONTA DE UTILIZADOR DA API
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        {{-- @switch($action) --}}
                        {{-- @case('create') {{ Breadcrumbs::render('users.create') }} @break --}}
                        {{-- @case('show') {{ Breadcrumbs::render('users.show', $user) }} @break --}}
                        {{-- @case('edit') {{ Breadcrumbs::render('users.edit', $user) }} @break --}}
                        {{-- @endswitch --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    ×
                                </button>
                                <h5>@choice('common.error', $errors->count())</h5>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card">

                            <form method="POST" action="{{ route('api.store') }}" class="pb-4">
                                @csrf

                                {{-- @if ($action === 'create') --}}
                                <div class="card-body row pb-0">

                                    <div class="form-group col-md-6">
                                        <label for="inputName">Empresa / Organização</label>
                                        <input required min="0" type="text" class="form-control" name="full_name"
                                            id="full_name" placeholder="Escreva o nome da empresa ou organização">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="inputEmail">E-mail</label>
                                        <input required min="0" type="email" class="form-control" name="email"
                                            id="email" placeholder="Escreva o e-mail">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="inputTelef1">Telemovel principal</label>
                                        <input required min="0" type="text" class="form-control"
                                            name="telefone_principal" id="telefone_principal"
                                            placeholder="Escreva o número de telefone principal">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="inputTelefon2">Telemovel alternativo</label>
                                        <input required min="0" type="text" class="form-control"
                                            name="telefone_altenativo" id="telefone_altenativo"
                                            placeholder="Escreva o número de telefone altenativo">
                                    </div>

                                </div>

                        </div>

                        {{-- @endif                                 --}}
                        @if (session('sucess'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('sucess') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-lg btn-success mb-3">
                            @icon('fas fa-plus-circle')
                            Gravar
                        </button>

                        </form>


                        <div class="card">
                            <div class="card-body">

                                <table id="users-table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Empresa / Organização</th>
                                            <th>E-mail</th>
                                            <th>Telemovel principal</th>
                                            <th>Telemovel secundário</th>
                                            <th>Token</th>
                                            <th>Keey</th>
                                            <th>@lang('common.created_by')</th>
                                            <th>@lang('common.created_at')</th>
                                            <th>@lang('common.updated_by')</th>
                                            <th>@lang('common.updated_at')</th>
                                            <th>@lang('common.actions')</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>
                        </div>


                        <!-- Modal  que apresenta a opção de eliminar -->
                        <div class="modal fade" id="delete_api_user" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Informação</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Deseja eliminar este usuário da API?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary rounded"
                                            data-dismiss="modal">Cancelar</button>
                                        <form action="{{ route('api.delete_user') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="id_api_user" id="id_api_user">
                                            <button type="submit" class="rounded btn btn-success">OK</button>
                                        </form>
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

    {{-- Modal preencher --}}




    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <form method="POST" action="{{ route('api.RHupdate') }}" class="pb-4">
            @method('PUT')
            @csrf
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Atualizar o estudante a uma entidade bolseira</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- @if ($action === 'create') --}}
                        <div class="card-body row pb-0">
                            <input type="hidden" name="chave" id="chave" />
                            <div class="form-group col-md-6">
                                <label for="inputName">Empresa / Organização</label>
                                <input required min="0" type="text" class="form-control" name="full_name"
                                    id="full_name_m" placeholder="Escreva o nome da empresa ou organização">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputEmail">E-mail</label>
                                <input required min="0" type="email" class="form-control" name="email"
                                    id="email_m" placeholder="Escreva o e-mail">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputTelef1">Telemovel principal</label>
                                <input required min="0" type="text" class="form-control"
                                    name="telefone_principal" id="telefone_principal_m"
                                    placeholder="Escreva o número de telefone principal">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputTelefon2">Telemovel alternativo</label>
                                <input required min="0" type="text" class="form-control"
                                    name="telefone_altenativo" id="telefone_altenativo_m"
                                    placeholder="Escreva o número de telefone altenativo">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputTelefon2">Estado</label>
                                <select name="estado" id="" class="form-control" required>
                                    <option value="" selected>Escolha o estado</option>
                                    <option value="1">Ativado</option>
                                    <option value="0">Desativado</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            id="cancelModalScholarship">Cancelar</button>
                        <button type="submit" class="btn btn-success">Confirmar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>





@endsection

@section('scripts')
    @parent
    <script>
        var curso = $("#curso");

        $(function() {



            $('#users-table').DataTable({
                ajax: '{!! route('api.lista_user') !!}',
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'telefone_principal',
                        name: 'telefone_principal'
                    },
                    {
                        data: 'telefone_altenativo',
                        name: 'telefone_altenativo'
                    },
                    {
                        data: 'token',
                        name: 'token',
                        visible: false
                    },
                    {
                        data: 'keey',
                        name: 'keey',
                        visible: false
                    },
                    {
                        data: 'created_by',
                        name: 'created_by',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                    },
                    {
                        data: 'update_by',
                        name: 'update_by',
                        visible: false
                    },
                    {
                        data: 'update_at',
                        name: 'update_at',
                        visible: false
                    },
                    {
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ],
                "lengthMenu": [
                    [10, 50, 100, 50000],
                    [10, 50, 100, "Todos"]
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
