@section('title', 'Lista Usuários por fases')
@extends('layouts.backoffice')
@section('styles')
    @parent
@endsection
@section('content')
    <div class="content-panel"style="padding: 0">
        @include('Users::candidate.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <br>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1> 
                            <span>Listar Estudante com  nº de matricula incorrecta</span>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item active" aria-current="page">
                                <a href="{{route('fase-candidatura')}}" title="voltar">
                                    <span>home/nº matrícula</span>
                                </a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        {{-- <button href="#" target="_blank" id="btn-fase" class="btn btn-success mb-3 ml-4">
                            @icon('fas fa-plus')
                            <span>criar fase candidatura</span>
                        </button> --}}
                    </div>
                    <div class="col-md-6">
                        <select name="signal" id="signal" class="form-control">
                            <option value="DIFFERENT">Diferente</option>
                            <option value="EQUAL">Igual</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <table id="users-table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ano_curricular</th>
                                            <th>nome</th>
                                            <th>email</th>                                            
                                            <th>code_matricula</th>
                                            <th>tamanho</th>
                                            <th>criado a </th>
                                            <th>actualizado a</th>
                                            <th>acções</th>
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
        let selectSignal = $('#signal');

        reloadDatas();

        selectSignal.change(function(e){
            console.log(selectSignal.val());
            $('#users-table').DataTable().clear().destroy();
            reloadDatas();
        });
        
        
        function reloadDatas() {
            $('#users-table').DataTable({
                ajax: '/users/matricula-incorrecta/ajax?signal='+selectSignal.val(),
                buttons: ['colvis', 'excel'],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'course_year',
                        name: 'course_year',
                        searchable: true
                    },                    
                    {
                        data: 'name',
                        name: 'name',
                        searchable: true
                    }, {
                        data: 'email',
                        name: 'email',
                        visible: true,
                        searchable: true
                    },
                    {
                        data: 'code_matricula',
                        name: 'code_matricula',
                        searchable: true
                    },
                    {
                        data: 'tamanho',
                        name: 'tamanho',
                        searchable: true
                    },                    
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: true
                    },     
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        searchable: true
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
        }    
    </script>
@endsection
