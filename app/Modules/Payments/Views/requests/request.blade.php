@switch($action)
    @case('show') @section('title',__('Payments::requests.request')) @break
@case('edit') @section('title',__('Payments::requests.edit_request')) @break
@endswitch

@extends('layouts.backoffice')

@section('content')
    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('show') @lang('Payments::requests.request') @break
                                @case('edit') @lang('Payments::requests.edit_request') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('show') {{ Breadcrumbs::render('requests.show', $request) }} @break
                            @case('edit') {{ Breadcrumbs::render('requests.edit', $request) }} @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">

                @switch($action)
                    @case('show')
                    {!! Form::model($request) !!}
                    @break
                    @case('edit')
                    {!! Form::model($request, ['route' => ['requests.update', $request->id], 'method' => 'put']) !!}
                    @break
                @endswitch

                <div class="row">
                    <div class="col">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    ×
                                </button>
                                <h5>@choice('common.error', $errors->count())</h5>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @switch($action)
                            @case('edit')
                            <button id="store-btn" type="submit" class="btn btn-sm btn-success mb-3">
                                <i class="fas fa-save"></i>
                                @lang('common.save')
                            </button>
                            @break
                            @case('show')
                            @if(auth()->user()->can('manage-requests-others'))
                                {{-- <a href="{{ route('requests.edit', $request->id) }}"
                                   class="btn btn-sm btn-warning mb-3">
                                    <i class="fas fa-edit"></i>
                                    @lang('common.edit')
                                </a> --}}
                            @endif
                            @break
                        @endswitch

                        @if(auth()->user()->can('create-requests-others'))
                            <div class="card">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="col">
                                            <label>@lang('Payments::payments.student')</label>
                                            <div>
                                               {{$userInfo->parameters[0]->pivot->value ?? $request->user->name}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif
                            @if ($action === 'edit')
                                <input type="text" id="balanceValue" name="balanceValue" hidden>
                                <input type="text" name="article_req_id" value="{{ $request->id }}" hidden>
                            @endif
                        {{-- exibir a caixa de seleção da disciplina caso o $request->article->id for igual a 41 ou 42 --}}
                        {{-- exibir a caixa de seleção caso também a action for igual a edit, caso for show... exibir so o nome da disciplina baseando-se no $request->article->id se for igual a 41 ou 42 --}}
                        {{-- !START EXPLANATION --}}
                        @if($action === 'edit' && $request->article->id === 41)
                            <div class="card">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Disciplina</label>
                                             <select name="disciplines" id="" class="form-control">
                                                <option value=""></option>
                                                @foreach ($userDisciplines->matriculation->disciplines as $discipline)
                                                    <option value="{{$discipline->id}}">#{{$discipline->code}} - {{$discipline->currentTranslation->display_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @elseif($action === 'edit' && $request->article->id === 42)
                            <div class="card">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Disciplina</label>
                                            <select name="disciplines" id="" class="form-control">
                                                <option value=""></option>
                                                @foreach ($userDisciplines->matriculation->disciplines as $discipline)
                                                    <option value="{{$discipline->id}}">#{{$discipline->code}} - {{$discipline->currentTranslation->display_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif
                        {{-- !END EXPLANATION --}}

                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    <div class="col">
                                        <label>@lang('Payments::articles.article')</label>
                                        <div>
                                            {{ $request->article->currentTranslation->display_name }}
                                        </div>
                                    </div>
                                </div>
                                @if ($request->year)
                                    <div class="col-3">
                                        <div class="col">
                                            <label>@lang('Payments::requests.year')</label>
                                            <div>
                                                {{ $request->year }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($request->month)
                                    <div class="col-3">
                                        <div class="col">
                                            <label>@lang('Payments::requests.month')</label>
                                            <div>
                                                {{ getLocalizedMonths()[$request->month - 1]["display_name"] }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row" style="margin-top: 8px">
                                <div class="col-3">
                                    <div class="col">
                                        <label>@lang('Payments::requests.base_value')</label>
                                        <div>
                                            {{ $request->base_value }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="col">
                                        <label>@lang('Payments::requests.extra_fees_value')</label>
                                        <div id="extra_fees_value">
                                            {{ $request->extra_fees_value }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="col">
                                        <label>@lang('Payments::requests.balance')</label>
                                        <div id="paid_value">0</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="col">
                                        <label>@lang('Payments::requests.paymentStatus')</label>
                                        <div id="request_status">
                                            @if ($request->status == "total")
                                            <span class='bg-success p-1 text-white'>PAGO</span>
                                            @elseif($request->status == "pending")
                                                <span class='bg-info p-1'>ESPERA</span>
                                            @elseif($request->status == "partial")
                                                <span class='bg-warning p-1'>PARCIAL</span>
                                            @elseif($request->status ==null)
                                                <span class='bg-info p-1'>ESPERA</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <hr>
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">@lang('Payments::requests.transactions.transactions')</h5>
                                    @if($action === 'edit')
{{--                                        <button data-toggle='modal' type='button' data-type='add'--}}
{{--                                                data-target='#modal_transaction' class='btn btn-sm btn-success mb-3'--}}
{{--                                                onclick="resetTransctionType()">--}}
{{--                                            @icon('fas fa-plus')--}}
{{--                                        </button>--}}
                                    @endif
                                    <div id="transactions"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>

{{--    @include('Payments::requests.modals.transaction')--}}
    @include('layouts.backoffice.modal_refund')
@endsection

@section('scripts')
    @parent
    <script>
        var request = @json($request);
        var baseValue = parseFloat(request.base_value);
        var extraFeesValue = parseFloat(request.extra_fees_value);
        var valueOffsetToZero = baseValue + extraFeesValue;
        var calculatedExtraFeesValue = extraFeesValue;
        var calculatedValue = 0;

        var initialData = [];

        // create dynamic list with initial data
        var data = request.transactions;

        $.each(data, function (k, v) {
            var info = v.transaction_info;
            var auxArray = [
                {
                    text: v.id,
                    value: v.id,
                    name: "transaction_id[]"
                }, {
                    text: v.type === 'debit' ? 'Débito' : 'Crédito',
                    value: v.type,
                    name: "transaction_type[]"
                }, {
                    text: v.pivot.value,
                    value: v.pivot.value,
                    name: "transaction_value[]"
                }, {
                    text: info && info.fulfilled_at ? info.fulfilled_at.slice(0, -9) : "-",
                    value: info && info.fulfilled_at ? info.fulfilled_at.slice(0, -9) : "",
                    name: "transaction_fulfilled_at[]"
                }, {
                    text: info && info.bank.display_name ? info.bank.display_name : "-",
                    value: info && info.bank_id ? info.bank_id : "",
                    name: "transaction_bank[]"
                }, {
                    text: info && info.reference ? info.reference : "-",
                    value: info && info.reference ? info.reference : "",
                    name: "transaction_reference[]"
                }, {
                    text: v.notes ? v.notes : '-',
                    value: v.notes ? v.notes : "",
                    name: "transaction_notes[]"
                },
            ];

            initialData.push(auxArray);
        });

        var transactionDT = new DynamicDatatableTransactions("#transactions", "table_transactions", [
            {
                text: "ID",
                name: "transaction_id[]"
            }, {
                text: "Tipo",
                name: "transaction_type[]"
            }, {
                text: "Valor",
                name: "transaction_value[]"
            }, {
                text: "Data",
                name: "transaction_fulfilled_at[]"
            }, {
                text: "Banco",
                name: "transaction_bank[]"
            }, {
                text: "Referência",
                name: "transaction_reference[]"
            }, {
                text: "Notas",
                name: "transaction_notes[]"
            },
        ], initialData, "Ações", "modal_transaction", "{!! $action !!}");

        transactionDT.initialize();

        function newRowFromModal(modal) {
            calculateValuePaid();
        }

        function deletedRowFromModal(modal) {
            calculateValuePaid();
        }

        function calculateValuePaid() {
            // calculateExtraFee();

            var calculated_value = 0;

            $.each(transactionDT.rows, function (k, v) {
                var type = v[1].value;
                var value = parseFloat(v[2].value);
                var operation = type === 'debit' ? -1 : 1;

                calculated_value += operation * value;
            });
            console.log(transactionDT)

            calculatedValue = valueOffsetToZero + calculated_value;
            $('#paid_value').html('<b>' + calculatedValue + '</b> <span style="color: grey"> (' + (baseValue + extraFeesValue) + ')</span>');
            var status = calculateRequestStatus();
            // $('#request_status').html(status);
        }

        function calculateExtraFee() {
            var requestDate = request.year && request.month ? new Date(request.year + '-' + request.month + '-1') : new Date(request.created_at);
            requestDate.setHours(0, 0, 0, 0);

            var extraFees = request.article.extra_fees.length ? request.article.extra_fees : null;

            if (extraFees) {
                extraFeesValue = 0;
                var highestDayDiff = 0;

                $.each(transactionDT.rows, function (k, v) {
                    var type = v[1].value;
                    var date = v[3].value;
                    if (type === 'payment') {
                        var transactionDate = new Date(date);
                        transactionDate.setHours(0, 0, 0, 0);
                        var diffInDays = Math.floor((transactionDate - requestDate) / (1000 * 60 * 60 * 24))
                        highestDayDiff = diffInDays > highestDayDiff ? diffInDays : highestDayDiff;
                    }
                });

                if (highestDayDiff) {
                    var normalPaymentDays = 0;

                    $.each(extraFees, function (k, v) {
                        if (v.fee_percent == 0) {
                            normalPaymentDays = v.max_delay_days;
                        }
                    })

                    var extraFeePercent = 0;
                    if (highestDayDiff > normalPaymentDays) {
                        // $.each(extraFees, function (k, v) {
                        //     if (v.fee_percent != 0 && v.max_delay_days <= (highestDayDiff - normalPaymentDays)) {
                        //         extraFeePercent = v.fee_percent > extraFeePercent ? v.fee_percent : extraFeePercent;
                        //     }
                        // })
                        var previousLimit = 0;
                        $.each(extraFees, function (kf, vf) {
                            if (vf.fee_percent != 0) {
                                var feeDayLimit = vf.max_delay_days + normalPaymentDays;
                                if (highestDayDiff > previousLimit) {
                                    extraFeePercent = vf.fee_percent;
                                }
                                previousLimit = feeDayLimit;
                            }
                        });
                    }

                    if (extraFeePercent) {
                        extraFeesValue = baseValue * (extraFeePercent / 100);
                    }
                    console.log('olá márcia daniel')
                }

                $('#extra_fees_value').text(extraFeesValue);
            }
        }

        function calculateRequestStatus() {
            $('#store-btn').attr('disabled', true);

            var statusList = @json($status_list);
            var html = null;

            if (calculatedValue === 0) {
                html = statusList.pending;
            } else if (calculatedValue === (baseValue + extraFeesValue)) {
                html = statusList.total;
            } else if (calculatedValue > 0 && calculatedValue < (baseValue + extraFeesValue)) {
                html = statusList.partial;
            } else {
                return statusList.error;
            }

            $('#store-btn').attr('disabled', false);
            return html;
        }

        function refundRowFromTable(table) {
            var originalType = table.find("input[name='transaction_type[]']").val();
            var refundTypeValue = originalType !== 'debit' ? 'debit' : 'credit';
            var refundTypeText = originalType !== 'debit' ? 'Débito' : 'Crédito';
            var refundValue = table.find("input[name='transaction_value[]']").val();

            transactionDT.addRow([
                {text: '', value: '', name: 'transaction_id'},
                {text: refundTypeText, value: refundTypeValue, name: 'transaction_type'},
                {text: refundValue, value: refundValue, name: 'transaction_value'},
                {text: '', value: '', name: 'transaction_fulfilled_at'},
                {text: '', value: '', name: 'transaction_bank'},
                {text: '', value: '', name: 'transaction_reference'},
                {text: 'Estorno', value: '', name: 'transaction_notes'},
            ]);

            $("#balanceValue").val(refundValue);
            calculateValuePaid();
        }

        var transactionInfoContainer = $('#transaction-info-container');

        var transactionTypeInput = $('#transaction_type');
        var transactionValueInput = $('#transaction_value');
        var transactionFulfilledInput = $('#transaction_fulfilled_at');
        var transactionBankInput = $('#transaction_bank');
        var transactionRefInput = $('#transaction_reference');
        var transactionNotesInput = $('#transaction_notes');

        var transactionType = null;

        function resetTransctionType() {
            transactionTypeInput.selectpicker('val', "");
            $('.btn.forlearn-btn.add').attr('disabled', false);
        }

        function resetTransaction() {
            transactionInfoContainer.attr('hidden', true);

            transactionValueInput.val(null);
            transactionFulfilledInput.val(null);
            transactionBankInput.selectpicker('val', "");
            transactionRefInput.val(null);
            transactionNotesInput.val(null);

            transactionValueInput.attr('disabled', true);
            transactionNotesInput.attr('disabled', true);

            transactionFulfilledInput.attr('required', false);
            transactionBankInput.attr('required', false);
            transactionRefInput.attr('required', false);
        }

        function enableBaseTransaction() {
            transactionValueInput.attr('disabled', false);
            transactionNotesInput.attr('disabled', false);
        }

        function enableTransactionInfo() {
            transactionFulfilledInput.attr('required', true);
            transactionBankInput.attr('required', true);
            transactionRefInput.attr('required', true);

            transactionInfoContainer.attr('hidden', false);
        }

        function generateReceiptForTransaction(id) {
            var myNewTab = window.open('about:blank', '_blank');
            let route = '{{ route('transactions.receipt', 0) }}'.slice(0, -1) + id
            $.ajax({
                method: "GET",
                url: route
            }).done(function (url) {
                myNewTab.location.href = url;
            });
        }

        $(function () {

            calculateValuePaid();

            $('body').on('change', '#transaction_type', function () {
                resetTransaction();

                transactionType = this.value;


                if (transactionType === 'payment') {
                    enableBaseTransaction();
                    enableTransactionInfo();
                }

                if (transactionType === 'adjust') {
                    enableBaseTransaction();
                }
            })
        });

        $(function () {
            $('input[name="transaction_reference"]').on('blur', function () {
                $('.btn.forlearn-btn.add').attr('disabled', true);
                Forlearn.checkIfModelFieldExists(this, '{{ route('transactions.reference_exists') }}')
            });
        });
    </script>
@endsection
