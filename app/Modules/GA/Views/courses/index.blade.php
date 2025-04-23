@section('title',__('GA::courses.courses'))
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
                        <h1 class="m-0 text-dark">@lang('GA::courses.courses')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('courses') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <a href="{{ route('courses.create') }}" class="btn btn-success btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>
                        <div class="card">
                            <div class="card-body">
                                <table id="courses-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('common.code')</th>
                                        <th>Código númerico</th>
                                        <th>@lang('translations.display_name')</th>
                                        <th>Departamento</th>
                                        <th>@lang('GA::courses.duration_value')</th>
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
            $('#courses-table').DataTable({
                ajax: '{!! route('courses.ajax') !!}',
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
                    visible: false
                },{
                    data: 'numeric_code',
                    name: 'numeric_code',
                    visible: false
                }, {
                    data: 'display_name',
                    name: 'ct.display_name'
                }, {
                    data: 'department_name',
                    name: 'dt.display_name'
                }, {
                    data: 'duration',
                    name: 'duration_value'
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
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
