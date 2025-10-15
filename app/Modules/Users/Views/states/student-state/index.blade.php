@section('title',"Gerir estados")
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

<div class="content-panel" style="padding: 0;">
    @include('Users::states.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Gerir estados</h1>
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

                        <a  href="{{ route('student_state.create')}}" class="btn btn-success btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>

                        <div class="card">
                            <div class="card-body">

                                <table id="students-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nº Matrícula</th>
                                        <th>Estudante</th>
                                        <th>Email</th>
                                        <th>Curso</th>
                                        <th>Estado</th>
                                        <th>Ocorreu a</th>
                                        <th>Criado Por</th>
                                        {{--<th>Criado a</th>--}}
                                        <th>Atualizado Por</th>
                                        {{--<th>Atualizado a</th>--}}
                                        <th>Ação</th>
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
         
                $("#students-table").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('student_state.ajax')}}",
                    buttons:[
                        'colvis',
                        'excel'
                    ], 
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'n_matriculation', name:'n_matriculation', visible: false, searchable: false},
                        {data: 'name', name:'u_p.value'},
                        {data: 'email', name:'u0.email'},
                        {data: 'course', name:'ct.display_name'},
                        {data: 'states', name:'states.name'},
                        {data: 'occurred_at', name:'users_states.occurred_at'},
                        {data: 'created_by', name:'u1.name', visible: false},
                        {data: 'updated_by', name: 'u2.name', visible: false},
                        //{data: 'created_at', name:'created_at'},
                        //{data: 'updated_at', name: 'updated_at'}
                        {data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ]
                    ,
                    language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                    }
                    
                });


               
            });
            
    </script>
@endsection
