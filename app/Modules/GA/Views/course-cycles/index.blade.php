@section('title',__('GA::course-cycles.course_cycles'))
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
                        <h1 class="m-0 text-dark">@lang('GA::course-cycles.course_cycles')</h1>
                        
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('course-cycles') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a href="{{ route('course-cycles.create') }}" class="btn btn-primary btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>

                        <div class="card">
                            <div class="card-body">

                                <table id="course-cycles-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('common.code')</th>
                                        <th>@lang('translations.display_name')</th>
                                        <th>@lang('translations.abbreviation')</th>
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
            $('#course-cycles-table').DataTable({
                ajax: '{!! route('course-cycles.ajax') !!}',
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [{
                    data: 'code',
                    name: 'code',
                    visible: false
                }, {
                    data: 'display_name',
                    name: 'cct.display_name'
                }, {
                    data: 'abbreviation',
                    name: 'cct.abbreviation',
                    visible: false
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
