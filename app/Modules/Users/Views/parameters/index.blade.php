@section('title',__('Users::parameters.parameters'))
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
                        <h1 class="m-0 text-dark">@lang('Users::parameters.parameters')</h1>
                        <span class="text-danger">
                            Cuidado para não fazer um parâmetro X chamar um parâmetro Y que por sua vez chama o parâmetro X.
                        </span>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('parameters') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a href="{{ route('parameters.create') }}" class="btn btn-primary btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>

                        <div class="card">
                            <div class="card-body">

                                <table id="parameters-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('common.code')</th>
                                        <th>@lang('translations.display_name')</th>
                                        <th>@lang('Users::parameters.type')</th>
                                        <th>@lang('Users::parameters.has_options')</th>
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
            $('#parameters-table').DataTable({
                ajax: '{!! route('parameters.ajax') !!}',
                buttons:[
                    'colvis','excel'
                    ],
                columns: [{
                    data: 'code',
                    name: 'code',
                    visible: false
                }, {
                    data: 'display_name',
                    name: 'pt.display_name'
                }, {
                    data: 'type',
                    name: 'type'
                }, {
                    data: 'has_options',
                    name: 'has_options'
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
