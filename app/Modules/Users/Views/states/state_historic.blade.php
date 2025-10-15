@section('title', 'Histórico dos estados')
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <div class="content-panel" style="padding: 0;">
        @include('Users::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Histórico dos estados</h1>
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
                        <div class="card">
                            <div class="card-body">

                                <table id="states-table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            {{-- <th>#</th> --}}
                                            <th>Sigla</th>
                                            <th>Estudante</th>
                                            <th>Estado</th>
                                            <th>Tipo</th>
                                            <th>Data</th>
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
        $(function() {

            $("#states-table").DataTable({
                processing: true,
                destroy: true,
                serverSide: true,
                searching:true,
                buttons: [
                    'colvis',
                    'excel'
                ],
                ajax: "{{ route('state_historic.ajax') }}",
                columns: [{
                        data: 'initials',
                        name: 'initials'
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'student_states',
                        name: 'student_states'
                    },
                    {
                        data: 'state_type',
                        name: 'state_type'
                    },
                    {
                        data: 'occurred_at',
                        name: 'occurred_at'
                    },

                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                }
            });
        });

        //Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
