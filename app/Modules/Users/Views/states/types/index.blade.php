@section('title',"Tipo de Estados")
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
                        <h1 class="m-0 text-dark">Tipo de Estados</h1>
                    </div>
                    <div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a  href="{{ route('types.create')}}" class="btn btn-primary btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>

                        <div class="card">
                            <div class="card-body">

                                <table id="types-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        {{--<th>#</th>--}}
                                        <th>Nome</th>
                                        <th>Criado Por</th>
                                        <th>Atualizado Por</th>
                                        <th>Criado a</th>
                                        <th>Atualizado a</th>
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
    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts')
    @parent
    <script>
        $(function(){
         
                $("#types-table").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('types.ajax')}}",
                    columns: [
                        {data: 'name', name:'name'},
                        {data: 'created_by', name:'created_by'},
                        {data: 'updated_by', name: 'updated_by', visible: false},
                        {data: 'created_at', name:'created_at', visible: false},
                        {data: 'updated_at', name: 'updated_at', visible: false},
                        {data: 'actions', name: 'action', orderable: false, searchable: false }
                    ]
                });
            });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
