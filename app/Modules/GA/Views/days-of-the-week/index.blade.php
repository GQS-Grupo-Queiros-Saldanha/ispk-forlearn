@section('title',__('Cms::days-of-the-week.days_of_the_week'))
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
                        <h1 class="m-0 text-dark">@lang('Cms::days-of-the-week.days_of_the_week')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('days-of-the-week') }}
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

                                <table id="days-of-the-week-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('common.code')</th>
                                        <th>@lang('translations.display_name')</th>
                                        <th>@lang('GA::days-of-the-week.is_start_of_week')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
                                        <th>@lang('common.created_by')</th>
                                        <th>@lang('common.updated_by')</th>
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
            $('#days-of-the-week-table').DataTable({
                ajax: '{!! route('days-of-the-week.ajax') !!}',
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [{
                    data: 'code',
                    name: 'code',
                    visible: false,
                }, {
                    data: 'display_name',
                    name: 'dt.display_name'
                }, {
                    data: 'is_start_of_week',
                    name: 'is_start_of_week'
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    visible: false
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
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
                    data: 'actions',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
                order: [2, "desc"],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
