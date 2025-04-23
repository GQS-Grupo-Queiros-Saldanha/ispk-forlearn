@section('title', 'Ola')
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px;">
        @include('Lessons::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Fórmulario</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('lessons') }}
                    </div>
                </div>
            </div>
        </div>
        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        {{-- <a href="{{ route('lessons.create') }}" class="btn btn-primary btn-sm mb-3">
                            <i class="fas fa-plus-square"></i>
                            @lang('common.new')
                        </a> --}}
                        <div class="card">
                            <div class="card-body">
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif                                
                                <form action="{{ isset($user->id) ? route('lessons.hello.update',$user->id) : route('lessons.hello.store') }}" class="row" method="POST">
                                    @csrf
                                    <div class="col-md-3">
                                        <label for="nome" class="">
                                            Nome:
                                        </label>
                                        <input type="text" name="nome" id="nome" class="form-control rounded"
                                            placeholder="digita o nome"  value="{{$user->nome ?? ''}}"/>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="sobrenome" class="">
                                            Sobrenome:
                                        </label>
                                        <input type="text" name="sobrenome" id="sobrenome" class="form-control rounded"
                                            placeholder="digita o sobrenome" value="{{$user->sobrenome ?? ''}}"/>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="genero">
                                            Digita o seu gênero:
                                        </label>
                                        <select name="genero" id="genero" class="form-control">
                                            @isset($generos, $user)
                                                @foreach($generos as $key => $value)
                                                    <option value="{{$key}}" {{$user->genero == $key ? "selected" : ""}}>{{$value}}</option>
                                                @endforeach
                                            @else
                                                <option value="MASCULINO">Masculino</option>
                                                <option value="FEMENINO">Femenino</option>
                                            @endisset
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="nascimento">
                                            Data nascimento
                                        </label>
                                        <input type="date" name="nascimento" id="nascimento"
                                            class="form-control rounded" value="{{$user->nascimento ?? ''}}"/>
                                    </div>
                                    
                                    @isset($user)
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-danger mt-2 ml-2 m-3">
                                            alterar
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-outline-info mt-2 ml-2 m-3">
                                            salvar
                                        </button>
                                    @endif
                                    <a href="{{ route('lessons.hello.show') }}" class="btn btn-outline-success mt-2 ml-3 m-3">
                                        relatorio
                                    </a>
                                </form>
                              
                                <table id="lessons-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Sobrenome</th>
                                        <th>Gênero</th>
                                        <th>Data nascimento</th>
                                        <th>actions</th>
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
@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            $('#lessons-table').DataTable({
                ajax: '{!! route('lessons.hello.get_all') !!}',
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },{
                        data: 'nome',
                        name: 'nome',
                    }, {
                        data: 'sobrenome',
                        name: 'sobrenome'
                    }, {
                        data: 'genero',
                        name: 'genero'
                    }, {
                        data: 'nascimento',
                        name: 'nascimento'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
