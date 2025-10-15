
<title>Eventos | forLEARN® by GQS</title>
@extends('layouts.backoffice_new')
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
        
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-6">
                        <h1> @lang('GA::events.events')</h1>
                    </div>
                    <div class="col-sm-6">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('events.index') }}">Eventos</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    listar
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        
                        @if(auth()->user()->hasAnyPermission(['manage-events']))
                        <a href="{{ route('events.create') }}" class="btn btn-success  mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a> 
                        @endif              
                        {{-- <button type="button" class="btn alert-primary" data-toggle="modal" data-target="#ModalSession">
                            <i class="fas fa-key"></i> Terminar sessão
                          </button> --}}
                          

                        <table id="events-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('translations.display_name')</th>
                                    {{-- <th>@lang('translations.description')</th> --}}
                                    <th>@lang('common.type') de Evento</th>
                                    <th>@lang('common.start_date')</th>
                                    <th>@lang('common.end_date')</th>
                                    <th>Hora de início</th>
                                    <th>Hora de fim</th>
                                    <th>@lang('GA::events.all_day')</th>
                                    <th>@lang('common.created_by')</th>
                                    <th>@lang('common.updated_by')</th>
                                    <th>@lang('common.created_at')</th>
                                    <th>@lang('common.updated_at')</th>
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

@endsection
@section('scripts')

    @parent
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> --}}

    <script>



        $(function() {
            $('#events-table').DataTable({
                ajax: '{!! route('events.ajax') !!}',
                // buttons: [
                //     'colvis',
                //     'excel'
                // ],
                columns: [{
                    data: 'display_name',
                    name: 'etr.display_name'
                }
                , 
                // {
                //     data: 'description',
                //     name: 'etr.descripton'
                // },
                {
                    data: 'type',
                    name: 'ett.display_name'
                }, {
                    data: 'start',
                    name: 'start'
                },
                {
                    data: 'end',
                    name: 'end'
                },
                {
                    data: 'start_time',
                    name: 'start_time'
                },
                {
                    data: 'end_time',
                    name: 'end_time'
                },
                 {
                    data: 'all_day',
                    name: 'all_day'
                }, {
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
                }, {
                    data: 'actions',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
                searching:true,
                "lengthMenu": [ [10, 50, 100, 50000], [10, 50, 100, "Todos"] ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        

    </script>



@endsection
