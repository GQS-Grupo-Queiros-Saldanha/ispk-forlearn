@section('title',__('Payments::requests.transactions.account'))
@extends('layouts.backoffice')

@section('styles')
    @parent

    <style>
        .flex-center-right {
            display: flex;
            justify-content: center;
            align-items: end;
            height: 100%;
            width: 100%;
            padding-right: 15px;
            flex-direction: column;
        }

        #user-balance,
        #user-personal-balance {
            padding: 0 2px 0 5px;
        }

        .positive-balance {
            color: green;
        }

        .negative-balance {
            color: red;
        }
    </style>
@endsection

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('Payments::requests.transactions.account')</h1>
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

                        @if(auth()->user()->can('manage-requests-others'))
                            <hr>
                            <div class="card">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group col">
                                            <label>@lang('Payments::requests.student')</label>
                                            {{ Form::bsLiveSelect('user', $users, null, ['required', 'placeholder' => '']) }}
                                        </div>
                                    </div>
                                    <div id="user-balance-container" class="col-4" hidden>
                                        <div class="flex-center-right">
                                            <div>
                                                Saldo em transacções: <span id="user-balance"
                                                                            class="positive-balance">0</span> AKZ
                                            </div>
                                            <div>
                                                Saldo em carteira: <span id="user-personal-balance"
                                                                         class="positive-balance">0</span> AKZ
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2" style="padding-top: 20px;">
                                        <a href="" class="btn btn-primary mb-3" id="call-pdf" target="_blank" >
                                            <i class="fas fa-file-pdf"></i> Gerar PDF
                                        </a>
                                    </div>

                                    <div class="col-2" style="padding-top: 20px;">
                                        {{-- {{ route('transaction_observations.show', 197)}} --}}
                                        <a href="" id="call-observation" target="_blank" class="btn btn-secondary">
                                                <i class="fas fa-plus"></i>
                                                Observações
                                        </a>
                                    </div>

                                </div>
                            </div>
                            <hr>
                        @endif

                        <div class="card">
                            <div class="card-body">

                                <table id="requests-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('Payments::requests.user')</th>
                                        <th>ID</th>
                                        <th>@lang('Payments::requests.transactions.type')</th>
                                        <th>@lang('Payments::requests.value')</th>
                                        <th>@lang('Payments::articles.article')</th>
                                        <th>@lang('Payments::banks.bank')</th>
                                        <th>@lang('Payments::requests.reference')</th>
                                        <th>@lang('Payments::requests.fulfilled_at')</th>
                                        <th>@lang('Payments::requests.transactions.notes')</th>
                                        <th>@lang('common.created_by')</th>
                                        <th>@lang('common.updated_by')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
                                        <th>@lang('common.actions')</th>
                                        <th>Disciplina</th>
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
        var dataTableBaseUrl = '{{ route('transactions.ajax', 0) }}'.slice(0, -1);
        var dataTablePayments = null;
        var balanceBaseUrl = '{{ route('transactions.ajax_balance', 0) }}'.slice(0, -1);
        var balanceContainer = $('#user-balance-container');
        var balanceText = $('#user-balance');
        var personalBalanceText = $('#user-personal-balance');
        var selectedUserId = null;

        $(function () {
            dataTablePayments = $('#requests-table').DataTable({
                ajax: dataTableBaseUrl + '{!! auth()->user()->id !!}',
                columns: [
                    {
                        data: 'user',
                        name: 'u0.name',
                        visible: false
                    }, {
                        data: 'id',
                        name: 'id'
                    }, {
                        data: 'type',
                        name: 'type'
                    }, {
                        data: 'transaction_value',
                        name: 'tar.value'
                    }, {
                        data: 'article',
                        name: 'article'
                    }, {
                        data: 'bank',
                        name: 'b.display_name',
                        visible: false
                    }, {
                        data: 'reference',
                        name: 'ti.reference',
                        visible: false
                    }, {
                        data: 'fulfilled_at',
                        name: 'ti.fulfilled_at'
                    }, {
                        data: 'notes',
                        name: 'notes',
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
                        name: 'created_at'
                    }, {
                        data: 'updated_at',
                        name: 'updated_at',
                        visible: false
                    }, {
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'discipline_name',
                        name: 'discipline_name'
                    }
                ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                }
            });

            @if(auth()->user()->can('manage-requests-others')) //
            var selectUser = $('#user');

            function switchDataOnDataTable(selectedUser) {
                dataTablePayments.ajax.url(dataTableBaseUrl + selectedUser).load();
            }

            function getStudent(selectedUser) {
                let pdfRoute = ("{{ route('transactions.pdf','id_user') }}").replace('id_user', selectedUser);
                let observationsRoute = ("{{ route('transaction_observations.show','id_user') }}").replace('id_user', selectedUser);

                $.get(pdfRoute, function (data) {
                    document.getElementById('call-pdf').setAttribute('href', pdfRoute);
                });

                $.get(observationsRoute, function (data) {
                    document.getElementById('call-observation').setAttribute('href', observationsRoute);
                 });

            }

            function updateStudentData(selectedUser) {
                if (selectedUser) {
                    $.get(balanceBaseUrl + selectedUser, function (data) {

                        balanceText
                            .removeClass(data.balance >= 0 ? 'negative-balance' : 'positive-balance')
                            .addClass(data.balance >= 0 ? 'positive-balance' : 'negative-balance');
                        balanceText.text(data.balance)

                        personalBalanceText
                            .removeClass(data.personal >= 0 ? 'negative-balance' : 'positive-balance')
                            .addClass(data.personal >= 0 ? 'positive-balance' : 'negative-balance');
                        personalBalanceText.text(data.personal)

                        balanceContainer.attr('hidden', false);
                    })
                } else {
                    balanceContainer.attr('hidden', true);
                }
            }

            if (!$.isEmptyObject(selectUser)) {
                selectedUserId = Utils.setSelectedUserOnLoad('selectedUserPayments', selectUser[0]);
                selectUser.selectpicker('val', selectedUserId);
                getStudent(selectedUserId);

                switchDataOnDataTable(selectedUserId);
                updateStudentData(selectedUserId);
                Utils.updatedSelectedUserInSession('selectedUserPayments', selectedUserId);

                selectUser.change(function () {
                    selectedUserId = parseInt(this.value);
                    getStudent(selectedUserId);

                    switchDataOnDataTable(selectedUserId);
                    updateStudentData(selectedUserId);
                    Utils.updatedSelectedUserInSession('selectedUserPayments', selectedUserId);
                });
            }
            @endif
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
