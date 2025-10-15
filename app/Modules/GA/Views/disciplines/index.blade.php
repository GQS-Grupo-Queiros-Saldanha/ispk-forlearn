@section('title',__('GA::disciplines.disciplines'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
<div class="content-panel" style="padding: 0px"> 
    @include("GA::navbar.navbar")
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('GA::disciplines.disciplines')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('disciplines') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a href="{{ route('disciplines.create') }}" class="btn btn-success btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>

                        <div class="card">
                            <div class="card-body">

                                <table id="disciplines-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('common.code')</th>
                                        <th>@lang('translations.display_name')</th>
                                        <th>Cursos</th>
                                        <th>UC</th>
                                        <th>@lang('GA::disciplines.areas')</th>
                                        <th>@lang('GA::disciplines.profile')</th>
                                        <th>@lang('GA::discipline-percentage.percentage')</th>
                                        <th>Transição obrigatória</th>
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
    </div>

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            $('#disciplines-table').DataTable({
                ajax: '{!! route('disciplines.ajax') !!}',
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },{
                    data: 'code',
                    name: 'code',
                    visible: false,
                    searchable: true
                }, {
                    data: 'display_name',
                    name: 'dt.display_name',
                    searchable: true
                }
                , {
                    data: 'course_name',
                    name: 'ct.display_name',
                    searchable: true
                },{
                    data: 'uc',
                    name: 'uc',
                    searchable: true
                }
                , {
                    data: 'areas',
                    name: 'dat.display_name',
                    searchable: true
                }, {
                    data: 'profile',
                    name: 'dpt.display_name',
                    searchable: true
                },{ 
                   data: 'percentage', 
                   name: 'percentage', 
                   searchable: true, 
                },{ 
                   data: 'mandatory_discipline', 
                   name: 'mandatory_discipline', 
                   searchable: true, 
                }, {
                    data: 'created_by',
                    name: 'created_by',
                    visible: false,
                    searchable: true
                }, {
                    data: 'updated_by',
                    name: 'updated_by',
                    visible: false,
                    searchable: true
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    visible: false,
                    searchable: true
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    visible: false,
                    searchable: true
                }, {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: true
                }],
                "lengthMenu": [ [10, 50, 100, 6000], [10, 50, 100, "Todos"] ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
