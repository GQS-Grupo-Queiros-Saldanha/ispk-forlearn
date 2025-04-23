@section('title', "Relatorio do RH")
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
                        <h1>RELATÓRIO de Recursos humanos</h1>
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
                    <div class="col-6">

                    </div>
                    <div class="col-4">
                        <div class="">
                            {{-- {{ Form::bsLiveSelect('roles', $roles, ['class' => "form-control", 'required']) }} --}}
                            <select data-live-search="true" class="selectpicker form-control form-control-sm" id="roles" data-actions-box="false" data-selected-text-format="values"  name="roles" tabindex="-98">
                                <option value=""></option>
                                @foreach ($roles as $role)
                                    <option value="{{$role->id}}">
                                            {{$role->currentTranslation->display_name}}
                                     </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <div id="tail">
                                <table id="users-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Cargo</th>
                                    </tr>
                                    </thead>
                                </table>
                                </div>

                                <table id="new-table" hidden class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Cargo (s) </th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
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
            listAll();
           $("#roles").change(function(){
               $("#body").empty();
               if ($.fn.dataTable.isDataTable("#new-table")) {
                 var table =  $('#new-table').DataTable();
                    table
                    .destroy();
               }
               $("#tail").prop('hidden', true);
                getUsers($("#roles").val());
           });
        });

        function listAll(){
                $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('reports.getUsers') !!}',
                columns: [
                {
                    data: 'DT_RowIndex', orderable: false, searchable: false
                },{
                    data: 'users',
                    name: 'users'
                },{
                    data: 'email',
                    name: 'email'
                }, {
                    data: 'roles',
                    name: 'roles'
                }
                ],

                "lengthMenu": [ [10, 50, 100, 50000], [10, 50, 100, "Todos"] ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                },

            });
            }

            function getUsers(keyword)
            {
            $('#new-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/reports/ajax_report_users_by_role/' + keyword,
                columns: [
                {
                    data: 'DT_RowIndex', orderable: false, searchable: false
                },{
                    data: 'users',
                    name: 'users'
                },{
                    data: 'email',
                    name: 'email'
                }, {
                    data: 'roles',
                    name: 'roles'
                }
                ],

                "lengthMenu": [ [10, 50, 100, 50000], [10, 50, 100, "Todos"] ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                },

            });

            $('#new-table').prop('hidden', false);
            //var table =  $('#users-table').DataTable({});
            //table.destroy();
              /* $.ajax({

            url: "/reports/ajax_report_users_by_role/" + keyword,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',

            success: function (dataResult) {
                var body = '';
                var i = 1;
                for (let a = 0; a < dataResult.length; a++) {

                body += "<tr>"
                body += "<td>"+ i++ +"</td><td>"+ dataResult[a].name +"</td><td>"+ dataResult[a].email +"</td><td>"+ dataResult[a].role +"</td>"
                body += "</tr>"

                }
                $("#body").append(body);
                $("#tail").prop('hidden', true);
                //$("#new-table").DataTable();
                $('#new-table').prop('hidden', false);

            }
            });*/
        }

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
