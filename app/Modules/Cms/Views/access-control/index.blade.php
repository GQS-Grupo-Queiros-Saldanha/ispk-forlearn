@section('title', __('Controlo de acessos'))
@extends('layouts.backoffice')
@section('styles')
    @parent
@endsection
@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <style>
        .list-group li button {
            border: none;
            background: none;
            outline-style: none;
            transition: all 0.5s;
        }

        .list-group li button:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            font-weight: bold
        }

        .subLink {
            list-style: none;
            transition: all 0.5s;
            border-bottom: none;
        }

        .subLink:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            border-bottom: #dfdfdf 1px solid;
        }
    </style>




    <div class="content-panel" style="padding:0">
        @include('Cms::access-control.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-6">
                        <h1 style="padding-left: 0px;">Controlo de acessos</h1>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        
                        <table id="events-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                   
                                    <th>#</th>
                                    <th>Nome completo</th>
                                    <th>email</th>
                                    <th>Cargo(s)</th>
                                    <th>Data de acesso</th>
                                    
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>



        
    </div>






    </div>

@endsection
@section('scripts')

    @parent
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> --}}

    <script>



        $(function() {
            $('#events-table').DataTable({
                ajax: '{!! route('access-control.ajax') !!}',
                buttons: [
                    'colvis',
                    'excel'
                ],
                columns: [
                {
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'full_name',
                    name: 'full_name.value'
                }
                , 
                {
                    data: 'email',
                    name: 'u.email'
                },
                {
                    data: 'roles',
                    name: 'roles'
                },
                {
                    data: 'acess_data',
                    name: 'log.data'
                } ],
                searching:true,
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        

    </script>



@endsection
