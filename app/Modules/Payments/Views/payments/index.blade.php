@section('title',__('Payments::payments.payments'))
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
                        <h1>@lang('Payments::payments.payments')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('payments') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a href="{{ route('account.create') }}" class="btn btn-primary btn-sm mb-3">
                            <i class="fas fa-plus-square"></i>
                            @lang('common.new')
                        </a>

                        @if(auth()->user()->can('manage-payments-others'))
                            <hr>
                            <div class="card">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>@lang('Payments::payments.student')</label>
                                            {{ Form::bsLiveSelect('user', $users, null, ['required']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif

                        <div class="card">
                            <div class="card-body">

                                <table id="payments-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('Payments::payments.user')</th>
                                        <th>@lang('Payments::articles.article')</th>
                                        <th>@lang('Payments::payments.total_value')</th>
                                        <th>@lang('Payments::articles.base_value')</th>
                                        <th>@lang('Payments::payments.fees')</th>
                                        <th>@lang('common.created_by')</th>
                                        <th>@lang('common.updated_by')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
                                        <th>@lang('Payments::payments.fulfilled_at')</th>
                                        <th>@lang('Payments::payments.status.status')</th>
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
        var dataTableBaseUrl = '{{ route('account.ajax', 0) }}'.slice(0, -1);
        var dataTablePayments = null;

        $(function () {
            dataTablePayments = $('#payments-table').DataTable({
                ajax: dataTableBaseUrl + '{!! auth()->user()->id !!}',
                columns: [
                    {
                        data: 'user',
                        name: 'u0.name',
                        visible: false
                    }, {
                        data: 'article',
                        name: 'at.display_name'
                    }, {
                        data: 'total_value',
                        name: 'total_value'
                    }, {
                        data: 'base_value',
                        name: 'base_value',
                        visible: false
                    }, {
                        data: 'extra_fee',
                        name: 'extra_fee',
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
                        data: 'fulfilled_at',
                        name: 'fulfilled_at',
                        visible: false
                    }, {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    }, {
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: 10,
                    render: function (data, type, row) {
                        return $('<span />', {html: data}).text();
                    }
                }],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                }
            });

                @if(auth()->user()->can('manage-payments-others'))
            var selectUser = $('#user');

            function switchDataOnDataTable(element) {
                dataTablePayments.ajax.url('/pt/payments/account_ajax/' + parseInt(element.value)).load();
            }

            if (!$.isEmptyObject(selectUser)) {
                switchDataOnDataTable(selectUser[0]);
                selectUser.change(function () {
                    switchDataOnDataTable(this);
                });
            }
            @endif
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
