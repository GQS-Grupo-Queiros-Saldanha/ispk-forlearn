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
                            <a href="{{route('fase-candidatura')}}" title="voltar">
                                @icon('fas fa-arrow-left')
                            </a>
                            <span>Listar Candidatos ({{$lectiveYear->display_name}}) na fase ({{$lectiveCandidate->fase}})</span>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item active d-none" aria-current="page">Candidaturas</li>
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
                    <div class="col-md-6"></div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                @include('Users::candidate.fase_candidatura.message')
                                @if(!$lectiveCandidate->is_termina)
                                    <p class="mb-2 text-danger">
                                        <i class="fas fa-info"></i>
                                        <span>A fase ({{$lectiveCandidate->fase}}) se encontra aberto [{{$lectiveCandidate->data_inicio}} - {{$lectiveCandidate->data_fim}}], não será permitido que os candidatos passam para próxima fase.</span>
                                    </p>
                                @endif
                                <table id="users-table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>code</th>
                                            <th>nome</th>
                                            <th>bi</th>
                                            <th>fase</th>
                                            <th>Ações</th>
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
    <div class="modal" id="modalFase" tabindex="-1" role="dialog">
        <form class="modal-dialog" role="document" id="form" action="#" method="POST">
            @csrf
            @method('POST')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transferência de fase.</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="message"></span>
                    <input type="hidden" name="user" id="user" value=""/>
                    <input type="hidden" name="fase_nova" id="fase_nova" value="{{$lectiveCandidate->fase+1}}"/>
                    <input type="hidden" name="lective_candidate_id" value="{{$lectiveCandidate->id}}" >
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btn-fase-action">guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">cancelar</button>
                </div>
            </div>
            <input type="hidden" name="url" id="url" value="{{route('fase.candidatura.ajax.list.users',$lectiveCandidate->id)}}" disabled>
        </form>
    </div>
    
@endsection
@section('scripts')
    @parent
    <script>
        reloadDatas();

        const ident = $('#user');
        const form = $('#form');
        const message = $('#message');
        const faseNova = $('#fase_nova');
        const modalFase = $('#modalFase');
        const formMethod = $("[name='_method']");
        
        function reloadDatas() {
            let url = $('#url');
            console.log(url.val())
            $('#users-table').DataTable({
                ajax: ''+url.val(),
                buttons: ['colvis', 'excel'],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'code',
                        name: 'code',
                        searchable: true
                    }, {
                        data: 'nome',
                        name: 'nome',
                        visible: true,
                        searchable: true
                    },
                    {
                        data: 'bi',
                        name: 'bi',
                        searchable: true
                    },
                    {
                        data: 'fase',
                        name: 'fase',
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
