<title>Tesouraria | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title')
    @lang('Payments::requests.transactions.transaction')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Transação
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Criar</li>
@endsection
@section('styles-new')
    <style>
        .fotoUserFunc {
            margin: 0px;
            padding: 0px;
            shape-outside: circle();
            clip-path: circle();
            border-radius: 50%;
            background-color: #c4c4c4;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 40%;
            width: 73px;
            height: 73px;
            -webkit-filter: brightness(.9);
            filter: brightness(.9);
            border: 3px solid #f9f9f9;
        }

        .form-group {
            padding-bottom: 5px;
            margin-bottom: 0px
        }

        .drag {
            cursor: pointer;
            float: left;
            margin-left: 5px;
            height: 100px;
            width: 100px;
        }

        #holder {
            margin: 0 auto;
            padding-top: 4px;
            padding-bottom: 4px;
        }

        #boxA {
            background-image: url('//{{ $_SERVER['HTTP_HOST'] }}/img/reciboBolseiro.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 40%;
        }

        #boxB {
            background-image: url('//{{ $_SERVER['HTTP_HOST'] }}/img/reciboNormal.png');
            background-size: cover;
            background-repeat: no-repeat;
            /* background-position: 40%; */
        }

        #boxB:hover {
            border-radius: 5px;
            border: #00b537 1px solid
        }

        #boxA:hover {
            border-radius: 5px;
            border: #00b537 1px solid
        }

        .request_tax {
            margin: 0px;
        }

        table tbody tr td {
            padding: 4px;
        }
    </style>
@endsection
@section('body')
    {!! Form::open(['route' => ['transaction-request.store', $user->id]]) !!}
    <div class="row">
        <div class="col-12">
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

        </div>
    </div>
    @if (!$getBolseiro->isEmpty())
        <div hidden class="pl-5 pr-5 pt-0 pb-0 alert-recibo">
            <div class="alert  alert-dismissible fade show rounded" role="alert" style="background: #ff6f44;">
                <strong class="text-white">Informação!</strong> Caro utilizador/a <b>{{ auth()->user()->name }}</b> para
                concluir o pagamento (Estundate bolseiro) escolhe o tipo de recibo a baixo.&nbsp;&nbsp;
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif
    <div class="col-md-12 row p-0 pr-0 mr-0">
        <div class="col-4">
            <div class="form-group col">
                <label>@lang('Payments::requests.transactions.type')</label>
                <select name="transaction_type" id="transaction_types" required
                    class="selectpicker form-control form-control-sm" data-live-search="true" data-actions-box="false"
                    data-selected-text-format="values" tabindex="-98">
                    @foreach ($credit_types as $credit_type)
                        <option value="{{ $credit_type['id'] }}">
                            {{ $credit_type['display_name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-2 pl-0  pr-0"hidden>
            <div class="form-group col">
                <label>Total</label>
                <input type="number" class="form-control" id="total_value" value="0" readonly name="totalValue">
            </div>
        </div>
        <div class="col-8 pr-0">
            @if (!$getBolseiro->isEmpty())
                <div class="pl-0 pr-3  d-flex justify-content-start  align-items-center" style="background: #fbfbfb;">
                @else
                    <div class="pl-0 pr-3  d-flex justify-content-end  align-items-center" style="background: #fbfbfb;">
            @endif
            @if (!$getBolseiro->isEmpty())
                <input type="hidden" id="tipo-recibo" name="tipo_recibo" value="Sem_recibo">

                <div style="margin-right: 40%;  " class="border-left  bg-white border-right p-0">
                    <article id="holder">
                        <div class="d-flex p-0 m-0">
                            <div>
                                <p class="p-0 m-0 text-center font-weight-light text-muted" style="font-size: 12px">Entidade
                                    Bolseira</p>
                                <div class="drag text-center p-3" id="boxA">
                                    <i hidden style="font-size: 4pc;color: #5ea4d3;"
                                        class="fa-solid fa-check check-boxA"></i>
                                </div>
                            </div>

                            <div>
                                <p class="p-0 m-0 text-center font-weight-light text-muted" style="font-size: 12px">
                                    Estudante</p>
                                <div class="drag text-center p-3" id="boxB">
                                    <i hidden style="font-size: 4pc; color: #5ea4d3;"
                                        class="fa-solid fa-check check-boxB"></i>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                {{-- @endif --}}
            @endif
            <div>
                <p><b style="font-size: 1.3pc;font-weight: normal;" class="pr-2 pb-3">{{ $user->fullname }}</b></p>
                @if (!$getBolseiro->isEmpty())
                    @php
                        $numberDesconto =
                            $getBolseiro[0]->desconto_scholarship_holder == null
                                ? 'Sem desconto'
                                : $getBolseiro[0]->desconto_scholarship_holder;
                    @endphp
                    <p class="text-left text-uppercase blockquote-footer">Estudante Bolseiro [ desconto:
                        <b>{{ $numberDesconto }}%</b> ]
                    </p>
                @endif

            </div>
            <div class="fotoUserFunc" style=""></div>
        </div>

    </div>

    </div>
    <div class="mr-0 ml-0 mt-3"
        style="background: white; padding-bottom: 1px; padding-right:0px; padding-left:0px;  margin-bottom: 17px;"
        id="transaction-info-container">
        <div class="form-group col-12 mt-2">
            {{-- <h5 class="border-top pt-3">Transacções bancárias</h5> --}}
            <h1 style="font-size:1pc;background: #f9f9f9; color: #2c2b2b;"
                class="border-bottom pt-3 mb-2 mt-1 pr-2 text-head-pamenty">Transacções bancárias</h1>
            <h1 hidden style="font-size:1pc;background: #f9f9f9; color: #2c2b2b;"
                class="border-bottom pt-3 mb-2 mt-1 pr-2 text-head-ajuste">Transacção por ajuste </h1>
        </div>
        <div class="field_wrapper">
            <div class="row">
                <input type="hidden" name="selectAnoLetivo" value="{{ $selectAnoLetivo }}">
                <div class="col pb-3">
                    {{ Form::bsText('transaction_value', null, ['class' => 'pb-0 mb-0', 'onkeypress' => 'somenteNumeros(this);', 'onkeydown' => 'somenteNumeros(this);', 'onkeyup' => 'somenteNumeros(this);', 'placeholder' => 'Digite o montante', 'required', 'min' => 1], ['label' => __('Payments::requests.value')]) }}
                    <small style="font-weight: bold" id="valor1" class="form-text text-muted pt-0 mt-0 pl-3"></small>
                </div>

                <div class="col">
                    {{ Form::bsDate('transaction_fulfilled_at', null, ['placeholder' => __('Payments::requests.fulfilled_at'), 'required', 'max' => date('Y-m-d')], ['label' => __('Payments::requests.fulfilled_at')]) }}
                </div>
                <div class="col div-bank">
                    <div class="form-group col">
                        <label>@lang('Payments::banks.bank')</label>
                        {{ Form::bsLiveSelect('transaction_bank', $banks, null, ['required', 'placeholder' => '']) }}
                    </div>
                </div>
                <div class="col div-referencia">
                    {{ Form::bsText('transaction_reference', null, ['required', 'class' => 'referencia-config'], ['label' => __('Payments::requests.reference')]) }}
                </div>
                <div hidden class="col div-entidade" style="padding-right: 30px;margin-top: -0.3pc">
                    <label for="exampleFormControlSelect1">Entidade</label>
                    <select class="selectpicker form-control form-control-sm entidade-ajust-conta" name="entidade_ajuste"
                        data-selected-text-format="values" tabindex="-98" data-live-search="true" id="">
                        {{-- <option selected></option> --}}
                        @foreach ($bankSem_referencia as $item)
                            @if ($item->type_conta_entidade == 'creditoAjuste')
                                <option value="{{ $item->id }}">{{ $item->display_name }}</option>
                            @endif
                        @endforeach

                    </select>
                </div>
            </div>
        </div>
        <div class="more-transation">
            @include('Payments::transactions.partials.informations')
        </div>
        <div class="form-group col-12">
            {{-- <div class="col-12"> --}}
            {{-- Form::bsText('transaction_notes', null, ['disabled'], ['label' => __('Payments::requests.transactions.notes')]) --}}
            <label for="">Notas</label>
            <textarea name="transaction_notes" id="" cols="20" rows="4" class="form-control"></textarea>
            {{-- </div> --}}
        </div>
    </div>

    <div class="form-group col-12" id="transaction-article-requests-container">
        <div class="d-flex border-bottom border-top m-0 p-0 " style="background-color: rgb(249, 249, 249)">
            <div class="mr-auto pl-1 pt-3 pb-2">
                <h6><i class="fa fa-wallet"></i> SALDO EM CARTEIRA:</h5>
                    <div class="col-12 ">
                        <b>Disponível: </b><?php
                        echo number_format($user->credit_balance ?: 0, 2, ',', '.');
                        ?> Kz
                    </div>
                    <div class="col-12 ">
                        <b>Após transacção: </b><span id="new_user_balance">0</span> Kz
                    </div>
                    @if ($user->credit_balance != 0)
                        <div class="form-check col-12">
                            <input type="checkbox" class="form-check-input" id="check-saldo" name="check_dado"
                                value="1">
                            <label style="color:balck; font-size:1pc" class="form-check-label mt-1" for="check-saldo">Não
                                deseja utilizar o SALDO EM CARTEIRA ?</label>
                        </div>
                    @endif

            </div>


            <div style="margin-right: 10px; padding: 10px; padding-left: 21px; padding-right: 15px; "
                class="border-left  bg-white border-right ">
                <div class="pt-1">
                    <h4>Total pago</h4>
                </div>
                <div class="text-center pt-2">
                    <h5><strong class="total_value"></strong> <small> Kz</small></h5>
                </div>
            </div>
            <div style="padding: 10px; background-color: #5ea4d3; color:white" class="border-left border-right">
                <div class="pt-1">
                    <h4>Total a pagar</h4>
                </div>
                <div class="text-center pt-2">
                    <h5><strong id="totalPagar"></strong> <small> Kz</small></h5>
                </div>
            </div>
        </div>

        {{-- <h5>Requerimentos em falta</h5> --}}
        <div class="row">
            <div class="col-12 ml-3 mr-3">
                <table style="width:100%">
                    <tr>
                        <th></th>
                        <th style="width: 600px;">Emolumento / Propina</th>
                        <th style="width: 120px;">Valor base</th>
                        {{-- <th>Valor em débito</th> --}}
                        <th style="width: 120px;">Multa</th>
                        <th style="width: 80px;">Anular?</th>
                        <th>Valor pago nesta transacção</th>
                        <th style="width: 220px;">Valor que fica pendente</th>
                        <th>Estado</th>
                    </tr>
                    @php($status_list = requestStatusList())
                    @foreach ($article_requests as $article_request)
                        {{-- @dd($article_request) --}}
                        <div class="col-12 mb-2">
                            <tr>
                                <td>
                                    <input data-id="{{ $article_request->month }}" type="checkbox"
                                        name="article_request_selected[]" value="{{ $article_request->id }}"
                                        onclick="updateSelectedRequests()" checked>
                                </td>
                                <?php
                                $nome_disciplina = null;
                                $nome_metrica = null;
                                foreach ($disciplines as $discipline) {
                                    if ($discipline->article_req_id == $article_request->id) {
                                        if ($discipline->discipline_id != null) {
                                            $nome_disciplina = " ($discipline->discipline_name)";
                                        }
                                    }
                                }
                                
                                foreach ($metrics as $metric) {
                                    if ($metric->article_req_id == $article_request->id) {
                                        if ($metric->metric_id != null) {
                                            $nome_metrica = " ($metric->nome)";
                                        }
                                    }
                                }
                                
                                $displayName = $article_request->article->currentTranslation->display_name;
                                if ($article_request->month && $article_request->year) {
                                    if ($article_request->month == 1) {
                                        $month = 'Janeiro';
                                    } elseif ($article_request->month == 2) {
                                        $month = 'Fevereiro';
                                    } elseif ($article_request->month == 3) {
                                        $month = 'Março';
                                    } elseif ($article_request->month == 4) {
                                        $month = 'Abril';
                                    } elseif ($article_request->month == 5) {
                                        $month = 'Maio';
                                    } elseif ($article_request->month == 6) {
                                        $month = 'Junho';
                                    } elseif ($article_request->month == 7) {
                                        $month = 'Julho';
                                    } elseif ($article_request->month == 8) {
                                        $month = 'Agosto';
                                    } elseif ($article_request->month == 9) {
                                        $month = 'Setembro';
                                    } elseif ($article_request->month == 10) {
                                        $month = 'Outubro';
                                    } elseif ($article_request->month == 11) {
                                        $month = 'Novembro';
                                    } elseif ($article_request->month == 12) {
                                        $month = 'Dezembro';
                                    }
                                    $displayName .= $nome_disciplina . ' - ' . " ($month $article_request->year)";
                                }
                                if ($nome_metrica != null) {
                                    $displayName .= $nome_disciplina . ' - ' . $nome_metrica;
                                }
                                ?>
                                <td>{{ $displayName }}</td>
                                {{-- <td style="margin-right: 9pc;width:9pc;">{{ $article_request->created_at }}</td> --}}



                                @if (count($getRegraImplementada) > 0)
                                    @if (in_array($article_request->month, $arrayMonth_getRegraImplementada))
                                        @foreach ($getRegraImplementada as $item)
                                            @if ($article_request->discipline_id == '' && $article_request->year != null && $article_request->month == $item->mes)
                                                <td style="width: 9pc" class="valorforaUso"
                                                    id="foraUso{{ $article_request->id }}"
                                                    id="foraUso{{ $article_request->id }}">
                                                    <?php echo number_format($item->valor ?: 0, 2, ',', '.'); ?><small>Kz</small>&nbsp; - &nbsp;<s>
                                                        <?php echo number_format($article_request->article->base_value ?: 0, 2, ',', '.'); ?><small>Kz</small></s>
                                                </td>
                                            @endif
                                        @endforeach
                                    @else
                                        @if ($article_request->discipline_id == '' && $article_request->year != null && $article_request->month != null)
                                            <td class="valorforaUso" id="valorUso{{ $article_request->id }}"
                                                data-id="{{ $article_request->id }}"
                                                style="margin-right: 10pc;width:6pc;">
                                                <?php echo number_format($article_request->article->base_value ?: 0, 2, ',', '.'); ?><small>Kz</small>
                                            </td>
                                        @endif
                                    @endif

                                    @if ($article_request->year == null && $article_request->month == null)
                                        <td class="valorforaUso" id="valorUso{{ $article_request->id }}"
                                            data-id="{{ $article_request->id }}" style="margin-right: 10pc;width:6pc;">
                                            <?php echo number_format($article_request->article->base_value ?: 0, 2, ',', '.'); ?><small>Kz</small>
                                        </td>
                                    @endif
                                    @if ($article_request->discipline_id != '' && $article_request->year != null && $article_request->month != null)
                                        <td class="valorforaUso" id="valorUso{{ $article_request->id }}"
                                            data-id="{{ $article_request->id }}" style="margin-right: 10pc;width:6pc;">
                                            <?php echo number_format($article_request->article->base_value ?: 0, 2, ',', '.'); ?><small>Kz</small>
                                        </td>
                                    @endif
                                    @if ($article_request->discipline_id != '' && $article_request->year == null && $article_request->month == null)
                                        <td class="valorforaUso" id="valorUso{{ $article_request->id }}"
                                            data-id="{{ $article_request->id }}" style="margin-right: 10pc;width:6pc;">
                                            <?php echo number_format($article_request->article->base_value ?: 0, 2, ',', '.'); ?><small>Kz</small>
                                        </td>
                                    @endif
                                @elseif(count($getRegraImplementEmolu) > 0 && count($getRegraImplementada) < 1)
                                    @if (in_array($article_request->month, $arrayMonth_getRegraImplementEmolu))
                                        @foreach ($getRegraImplementEmolu as $item)
                                            @if ($article_request->discipline_id == '' && $article_request->year != null && $article_request->month == $item->mes)
                                                <td style="width: 9pc" class="valorforaUso"
                                                    id="foraUso{{ $article_request->id }}"
                                                    id="foraUso{{ $article_request->id }}">
                                                    <?php echo number_format($item->valor ?: 0, 2, ',', '.'); ?><small>Kz</small>&nbsp; - &nbsp;<s>
                                                        <?php echo number_format($article_request->article->base_value ?: 0, 0, ',', '.'); ?><small>Kz</small></s>
                                                </td>
                                            @endif
                                        @endforeach
                                    @else
                                        @if ($article_request->discipline_id == '' && $article_request->year != null && $article_request->month != null)
                                            <td class="valorforaUso" id="valorUso{{ $article_request->id }}"
                                                data-id="{{ $article_request->id }}"
                                                style="margin-right: 10pc;width:6pc;">
                                                <?php echo number_format($article_request->article->base_value ?: 0, 2, ',', '.'); ?><small>Kz</small>
                                            </td>
                                        @endif
                                    @endif
                                    @if ($article_request->year == null && $article_request->month == null)
                                        <td class="valorforaUso" id="valorUso{{ $article_request->id }}"
                                            data-id="{{ $article_request->id }}" style="margin-right: 10pc;width:6pc;">
                                            <?php echo number_format($article_request->article->base_value ?: 0, 2, ',', '.'); ?><small>Kz</small>
                                        </td>
                                    @endif
                                    @if ($article_request->discipline_id != '' && $article_request->year != null && $article_request->month != null)
                                        <td class="valorforaUso" id="valorUso{{ $article_request->id }}"
                                            data-id="{{ $article_request->id }}" style="margin-right: 10pc;width:6pc;">
                                            <?php echo number_format($article_request->article->base_value ?: 0, 2, ',', '.'); ?><small>Kz</small>
                                        </td>
                                    @endif
                                    @if ($article_request->discipline_id != '' && $article_request->year == null && $article_request->month == null)
                                    @endif
                                @else
                                    <td class="valorforaUso" id="valorUso{{ $article_request->id }}"
                                        data-id="{{ $article_request->id }}" style="margin-right: 10pc;width:6pc;">
                                        <?php echo number_format($article_request->article->base_value ?: 0, 2, ',', '.'); ?><small>Kz</small>
                                    </td>
                                @endif





                                <td>
                                    <p class="request_tax">
                                        <ass id="request_tax_{{ $article_request->id }}">
                                            {{ $article_request->extra_fees_value != 0 ? number_format(0, 2, ',', '.') : number_format(0, 2, ',', '.') }}
                                        </ass>
                                        {{-- <sup>
                                            {{ $article_request->extra_fees_value != 0 ? $article_request->extra_fees_value : '' }}</sup> --}} kz
                                    </p>

                                </td>

                                @if ($article_request->year != '' && $article_request->month != '')
                                    @if (auth()->user()->hasAnyPermission(['cancel-mult']))
                                        <td style="margin-left: 10pc">
                                            <input value="{{ $article_request->id }}"
                                                data-columns="{{ $article_request->id }}"
                                                id="checados{{ $article_request->id }}"
                                                data-id="{{ $article_request->month }}" type="checkbox"
                                                name="checados[]">
                                        </td>
                                    @else
                                        <td> </td>
                                    @endif
                                @else
                                    <td> </td>
                                @endif


                                <td id="request_to_pay_{{ $article_request->id }}">{{ 0 }}</td>

                                @if (count($getRegraImplementada))
                                    @if (in_array($article_request->month, $arrayMonth_getRegraImplementada))
                                        @foreach ($getRegraImplementada as $item)
                                            @if ($article_request->discipline_id == '' && $article_request->year != null && $article_request->month == $item->mes)
                                                <td id="request_balance_{{ $article_request->id }}"
                                                    class="request_balance">
                                                    <span
                                                        class="{{ $item->valor < 0 ? 'text-danger' : 'text-success' }}">-{{ $item->valor }}</span>
                                                    {{-- &nbsp; -  &nbsp;<s><span class="{{ $article_request->balance < 0 ? 'text-danger' : 'text-success' }}">{{ $article_request->balance }}</span></s>  --}}
                                                </td>
                                            @endif
                                        @endforeach
                                    @else
                                        @if ($article_request->discipline_id == '' && $article_request->year != null && $article_request->month != null)
                                            <td id="request_balance_{{ $article_request->id }}" class="request_balance">
                                                <span
                                                    class="{{ $article_request->balance < 0 ? 'text-danger' : 'text-success' }}">{{ $article_request->balance }}</span>
                                            </td>
                                        @endif
                                    @endif

                                    @if ($article_request->year == null && $article_request->month == null)
                                        <td id="request_balance_{{ $article_request->id }}" class="request_balance">
                                            <span
                                                class="{{ $article_request->balance < 0 ? 'text-danger' : 'text-success' }}">{{ $article_request->balance }}</span>
                                        </td>
                                    @endif
                                    @if ($article_request->discipline_id != '' && $article_request->year != null && $article_request->month != null)
                                        <td id="request_balance_{{ $article_request->id }}" class="request_balance">
                                            <span
                                                class="{{ $article_request->balance < 0 ? 'text-danger' : 'text-success' }}">{{ $article_request->balance }}</span>
                                        </td>
                                    @endif
                                    @if ($article_request->discipline_id != '' && $article_request->year == null && $article_request->month == null)
                                        <td id="request_balance_{{ $article_request->id }}" class="request_balance">
                                            <span
                                                class="{{ $article_request->balance < 0 ? 'text-danger' : 'text-success' }}">{{ $article_request->balance }}</span>
                                        </td>
                                    @endif
                                @elseif(count($getRegraImplementEmolu) > 0 && count($getRegraImplementada) < 1)
                                    @if (in_array($article_request->month, $arrayMonth_getRegraImplementEmolu))
                                        @foreach ($getRegraImplementEmolu as $item)
                                            @if ($article_request->discipline_id == '' && $article_request->year != null && $article_request->month == $item->mes)
                                                <td id="request_balance_{{ $article_request->id }}"
                                                    class="request_balance">
                                                    <span
                                                        class="{{ $item->valor < 0 ? 'text-danger' : 'text-success' }}">-{{ $item->valor }}</span>
                                                    {{-- &nbsp; -  &nbsp;<s><span class="{{ $article_request->balance < 0 ? 'text-danger' : 'text-success' }}">{{ $article_request->balance }}</span></s>  --}}
                                                </td>
                                            @endif
                                        @endforeach
                                    @else
                                        @if ($article_request->discipline_id == '' && $article_request->year != null && $article_request->month != null)
                                            <td id="request_balance_{{ $article_request->id }}" class="request_balance">
                                                <span
                                                    class="{{ $article_request->balance < 0 ? 'text-danger' : 'text-success' }}">{{ $article_request->balance }}</span>
                                            </td>
                                        @endif
                                    @endif
                                    @if ($article_request->year == null && $article_request->month == null)
                                        <td id="request_balance_{{ $article_request->id }}" class="request_balance">
                                            <span
                                                class="{{ $article_request->balance < 0 ? 'text-danger' : 'text-success' }}">{{ $article_request->balance }}</span>
                                        </td>
                                    @endif
                                    @if ($article_request->discipline_id != '' && $article_request->year != null && $article_request->month != null)
                                        <td id="request_balance_{{ $article_request->id }}" class="request_balance">
                                            <span
                                                class="{{ $article_request->balance < 0 ? 'text-danger' : 'text-success' }}">{{ $article_request->balance }}</span>
                                        </td>
                                    @endif
                                    @if ($article_request->discipline_id != '' && $article_request->year == null && $article_request->month == null)
                                        {{-- <td id="request_balance_{{$article_request->id}}" class="request_balance"> --}}
                                        {{-- <span class="{{ $article_request->balance < 0 ? 'text-danger' : 'text-success' }}">{{ $article_request->balance }}</span> --}}
                                        {{-- </td> --}}
                                    @endif
                                @else
                                    <td id="request_balance_{{ $article_request->id }}" class="request_balance">
                                        <span
                                            class="{{ $article_request->balance < 0 ? 'text-danger' : 'text-success' }}">{{ $article_request->balance }}</span>
                                    </td>
                                @endif

                                <td id="request_status_{{ $article_request->id }}">
                                    {!! $status_list[$article_request->status] !!}
                                </td>
                            </tr>
                        </div>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="mt-4 mb-2" id="groupDonePaymentButton" hidden>
            <button style="border-radius: 6px;" type="button"
                class="btn btn btn-success btn btn-lg btn-submeter float-right" id="donePaymentButton">
                @icon('fas fa-plus-circle')
                Concluir pagamento
            </button>
        </div>
    </div>

    <div class="modal fade modal_Transacao" id="exampleModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered rounded" role="document">
            <div class="modal-content rounded" style="background-color: #002d3a;">
                <input type="hidden" name="referenciaFalca" value="0" id="referencia-falca">
                <div class="modal-header">
                    <h4 style="color:#ededed" class="modal-title" id="exampleModalLabel">Informação</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body text mt-4">
                    <h3 style="color: #ededed" id="texto-pagamento"> Tem a certeza que os dados de pagamentos estão todos
                        correctos?</h3>
                </div>
                <div class="modal-footer">
                    <button style="border-radius: 6px; background:#01c93e" type="button"
                        class="btn btn-lg text-white btn-submeter" data-dismiss="modal">Cancelar</button>
                    <button style="border-radius: 6px; background:#20c7f9" data-toggle="modal"
                        data-target="#staticBackdrop" type="submint" class="btn btn-lg text-white btn-submeter"
                        id="confirmar-btn">OK</button>
                </div>
                <div class="p-2" style="color: white;background:#00b537" id="div-alerta-transacao">
                    <h4>Caro utilizador a <b>forLEARN</b> detectou que alguns dados não foram inserido corretamente, por
                        favor verifique!</h4>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection
@section('models')
    <div style="z-index: 1900;" class="modal fade modal_loader" id="staticBackdrop" data-backdrop="static"
        data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
        </div>
    </div>

    <div class="modal fade modal_Referencia" id="modal_Referencia" tabindex="-1" role="dialog"
        data-bs-backdrop="static" aria-labelledby="modal_Referencia" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered rounded" role="document">
            <div class="modal-content rounded" style="background-color: #002d3a;">
                <div class="modal-header">
                    <h4 style="color:#ededed" class="modal-title" id="modal_Referencia">ALERTA !</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body text mt-4">
                    <h4 style="color: #ededed" id="texto-reference"> </h4>
                </div>
                <div class="modal-footer">
                    <a style="border-radius: 6px; background:#2196f3" target="_blank"
                        class="btn btn-lg text-white btn-submeter btn-get-reference"><i class="fa fa-eye"></i> Ver
                        detalhes</a>
                    <a style="border-radius: 6px; background:#000000" target="_blank"
                        class="btn btn-lg text-white btn-submeter btn-get-recibo"><i class="fa fa-file-pdf"></i>
                        Factura/Recibo</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-new')
    @parent
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <script>
        // Criar variavel
        var transactionInfoContainer = $('#transaction-info-container');
        // var div_credito_ajusto=$('.div-credito-ajusto');
        var more_transation = $(".more-transation");
        var transactionArticleRequestContainer = $('#transaction-article-requests-container');

        var transactionTypeInput = $('#transaction_type');
        var transactionValueInput = $('#transaction_value');



        var firstTransactionValueInput = $('#transaction_value_1');
        var secondTransactionValueInput = $("#transaction_value_2");
        var thirdTransactionValueInput = $("#transaction_value_3");
        var fourthTransactionValueInput = $("#transaction_value_4");

        var transactionFulfilledInput = $('#transaction_fulfilled_at');
        var transactionBankInput = $('#transaction_bank');
        var transactionRefInput = $('#transaction_reference');
        var transactionNotesInput = $('#transaction_notes');

        var transaction_fulfilled_at_1ValueInput = $("#date_1");
        var transaction_fulfilled_at_2ValueInput = $("#date_2");
        var transaction_fulfilled_at_3ValueInput = $("#date_3");
        var transaction_fulfilled_at_4ValueInput = $("#date_4");

        var referencia_1 = null;
        var referencia_2 = null;
        var referencia_3 = null;
        var referencia_4 = null;
        var referencia_5 = null;





        var transactionType = null;
        var transactionValue = null;
        var transactionDate = null;
        var firstTransactionValue = null;
        var secondTransactionValue = null;
        var thirdTransactionValue = null;
        var fourthTransactionValue = null;
        var fifthTransactionValue = null;

        var userBalance = parseFloat('{{ $user->credit_balance }}');

        var requestsData = @json($article_requests);
        var requests = JSON.parse(JSON.stringify(requestsData));
        var checkedRequestIds = [];
        var all_month = [];
        var ativo_month = null;
        var sem_Refencia = null;
        var multa_emolument = [];
        var valorPagar = 0;
        var valorPago = 0;
        var totalPagar = $("#totalPagar");
        var total_value = $(".total_value");

        var transaction_types = $("#transaction_types")
        let ref1 = true,
            ref2 = true,
            ref3 = true;
        ref4 = true;
        ref5 = true;

        var statusList = @json($status_list);
        // termino da criação das variaveis

        $("#boxA").click(function(e) {
            $(".check-boxB").attr('hidden', true)
            $(".check-boxA").removeAttr('hidden')
            $("#tipo-recibo").val("recibo_bolseiro");
            $(".alert-recibo").attr('hidden', true);
            //warning("Clicado Primeira caixa")
        });

        $("#boxB").click(function(e) {
            $(".check-boxA").attr('hidden', true)
            $(".check-boxB").removeAttr('hidden')
            $("#tipo-recibo").val("recibo_normal");
            $(".alert-recibo").attr('hidden', true);
        });

        $("#check-saldo").click(function(e) {
            userBalance = $("#check-saldo").is(":checked") ? 0 : parseFloat('{{ $user->credit_balance }}')
            paymentInfoReady();
        });

        $("#confirmar-btn").click(function() {

            $(".modal_Transacao").css('visibility', 'hidden')
        });

        $("#donePaymentButton").click(function() {

            if (Number.parseFloat(transactionValueInput.val()) == 0) {
                transactionValueInput.css('border', 'red 2px solid')
            } else {

                setTimeout(() => {
                    if (ref1 == false || ref2 == false || ref3 == false || ref4 == false || ref5 == false) {
                        $("#confirmar-btn").attr('hidden', true);
                        $("#div-alerta-transacao").attr('hidden', false);
                        $("#texto-pagamento").attr('hidden', true);
                        $("#exampleModal").modal('show');
                        $("#groupDonePaymentButton").prop('hidden', true);
                        $("#referencia-falca").val(1);

                    } else {
                        if ($("#tipo-recibo").val() == "Sem_recibo") {
                            $(".alert-recibo").attr('hidden', false);

                        } else {
                            $("#exampleModal").modal('show');
                            $("#confirmar-btn").attr('hidden', false);
                            $("#div-alerta-transacao").attr('hidden', true);
                            $("#texto-pagamento").attr('hidden', false);
                            $("#referencia-falca").val(0);
                        }


                    }
                }, 2020);


                transactionValueInput.css('border', '#ced4da 1px solid')
            }
        });


        function somenteNumeros(num) {
            var er = /[^0-9.]/;
            er.lastIndex = 0;
            var campo = num;
            if (er.test(campo.value)) {
                campo.value = "";
            }
        }

        getAll_checked()
        $("#confirmar-btn").click(function() {
            if (sem_Refencia == true) {
                if (transactionValueInput.val() != "") {
                    if (transactionFulfilledInput.val() == "" && transactionBankInput.val() == "" &&
                        transactionRefInput.val() == "") {
                        $('#transaction_value').prop('required', true);
                        $('#transaction_reference').prop('required', true);
                        $("#transaction_bank").prop('required', true);
                    } else {
                        $('#transaction_value').prop('required', false);
                        $('#transaction_reference').prop('required', false);
                        $("#transaction_bank").prop('required', false);
                    }
                } else {
                    $('#transaction_value').prop('required', false);
                    $('#transaction_reference').prop('required', false);
                    $("#transaction_bank").prop('required', false);
                    // // $("#transaction_bank").val(123);
                }

            }
        })

        function resetTransctionType() {
            transactionTypeInput.selectpicker('val', "");
            // $('.btn.forlearn-btn.add').attr('disabled', false);
        }

        function resetTransaction() {
            // transactionInfoContainer.attr('hidden', true);
            // transactionArticleRequestContainer.attr('hidden', true);
            requests = JSON.parse(JSON.stringify(requestsData));

            transactionValueInput.val(null);
            firstTransactionValueInput.val(null);
            secondTransactionValueInput.val(null);
            thirdTransactionValueInput.val(null);
            fourthTransactionValueInput.val(null);

            transactionFulfilledInput.val(null);
            transactionBankInput.selectpicker('val', "");
            transactionRefInput.val(null);
            transactionNotesInput.val(null);

            transactionValue = null;
            transactionDate = null;

            transactionValueInput.attr('disabled', true);
            transactionNotesInput.attr('disabled', true);

            transactionFulfilledInput.attr('required', false);
            transactionBankInput.attr('required', false);
            transactionRefInput.attr('required', false);
            valorPagar = 0

            resetRequestsTable();
            calculateTaxValues();
            assingValueToArticleRequests();
        }

        function enableBaseTransaction() {
            transactionValueInput.attr('disabled', false);
            firstTransactionValueInput.val(null);
            secondTransactionValueInput.val(null);
            thirdTransactionValueInput.val(null);
            fourthTransactionValueInput.val(null);
            // transactionNotesInput.attr('disabled', false);
            // div_credito_ajusto.attr('hidden',false)
            more_transation.attr('hidden', true)
            // transactionArticleRequestContainer.attr('hidden', false);
            // transactionInfoContainer.attr('hidden', false);

            $(".text-head-ajuste").attr('hidden', false)
            $(".text-head-pamenty").attr('hidden', true)

            $(".div-bank").attr('hidden', true);
            $(".div-referencia").attr('hidden', true);
            $(".div-entidade").attr('hidden', false);
            $("#valor1").text(null)
            $("#valor2").text(null)
            $("#valor3").text(null)
            $("#valor4").text(null)
            $("#valor5").text(null)
            $("#total_value").val(null)

            valorPago = Number.parseFloat(0)
            total_value.text(valorPago.toLocaleString('pt-br', {
                minimumFractionDigits: 2
            }))

        }

        function enableTransactionInfo() {
            $(".div-bank").attr('hidden', false);
            $(".div-referencia").attr('hidden', false);
            $(".div-entidade").attr('hidden', true);

            transactionFulfilledInput.attr('required', true);
            transactionBankInput.attr('required', true);
            transactionRefInput.attr('required', true);
            // div_credito_ajusto.attr('hidden',false)
            more_transation.attr('hidden', false)
            // transactionInfoContainer.attr('hidden', false);
            // transactionArticleRequestContainer.attr('hidden', false);

            $(".text-head-ajuste").attr('hidden', true)
            $(".text-head-pamenty").attr('hidden', false)
            $("#groupDonePaymentButton").attr('hidden', true)
            $("#valor1").text(null)
            $("#valor2").text(null)
            $("#valor3").text(null)
            $("#valor4").text(null)
            $("#valor5").text(null)
            $("#total_value").val(null)
            valorPago = Number.parseFloat(0)
            total_value.text(valorPago.toLocaleString('pt-br', {
                minimumFractionDigits: 2
            }))




        }

        function generateReceiptForTransaction(id) {
            var myNewTab = window.open('about:blank', '_blank');
            let route = '{{ route('transactions.receipt', 0) }}'.slice(0, -1) + id;
            $.ajax({
                method: "GET",
                url: route
            }).done(function(url) {
                myNewTab.location.href = url;
            });
        }

        function getAll_checked() {
            var checkboxes = document.getElementsByName("article_request_selected[]");
            var checkedIds = [];
            var checked_month = [];
            var numb = null;
            $.each(checkboxes, function(k, v) {
                if (v.checked) {
                    numb = $(this).attr("data-id")
                    if (numb == "") {
                        numb = 0
                    } else {

                    }
                    all_month.push(parseInt(numb))
                }
            });
        }

        function updateSelectedRequests() {
            var checkboxes = document.getElementsByName("article_request_selected[]");
            var checkedIds = [];
            var checked_month = [];

            var numb = null;
            $.each(checkboxes, function(k, v) {
                if (v.checked) {
                    checkedIds.push(parseInt(v.value));
                    numb = $(this).attr("data-id")
                    if (numb == "") {
                        numb = 0
                    } else {

                    }
                    checked_month.push(parseInt(numb))
                } else {

                    // all_month.splice([k], 1)

                }
            });
            if (checked_month.length > 0) {
                $.each(checked_month, function(index, value) {
                    if (value == all_month[index] && transactionValueInput.val() == "" && transactionFulfilledInput
                        .val() == "" && transactionBankInput.val() == "" && transactionRefInput.val() == "") {
                        ativo_month = true;
                        $("#groupDonePaymentButton").attr('hidden', true)
                    } else {
                        $("#groupDonePaymentButton").attr('hidden', false)


                    }
                });
            } else {
                ativo_month = false;
                $("#groupDonePaymentButton").attr('hidden', true)

            }


            checkedRequestIds = checkedIds;
            resetRequestsTable();
            calculateTaxValues();
            assingValueToArticleRequests();
        }

        function paymentInfoReady() {
            var ready = transactionValue !== null &&
                (
                    transactionType !== 'payment' ||
                    (
                        transactionType === 'payment' &&
                        transactionDate !== null
                    )
                );
            if (ready) {
                resetRequestsTable();
                calculateTaxValues();
                assingValueToArticleRequests();
            }
            if (userBalance != 0 && ready == false) {
                resetRequestsTable();
                calculateTaxValues();
                assingValueToArticleRequests();
            }
        }

        function calculateTaxValues() {
            if (transactionDate) {
                $.each(requests, function(k, v) {
                    var fees = v.article.extra_fees;
                    if (fees.length) {
                        function byFeePercent(a, b) {
                            if (a.fee_percent > b.fee_percent) return 1;
                            if (b.fee_percent > a.fee_percent) return -1;
                            return 0;
                        }

                        fees.sort(byFeePercent);

                        function zeroPercentFee(v) {
                            return v.fee_percent === 0;
                        }

                        var zeroTax = fees.find(zeroPercentFee);

                        var noTaxDays = zeroTax ? zeroTax.max_delay_days : 0;

                        var requestDate = v.year && v.month ? new Date(v.year + '-' + v.month + '-1') : new Date(v
                            .created_at);

                        var latestTransaction = latestPaymentTransactionDate(v.transactions);

                        var diffTime = 0;
                        if (latestTransaction >= requestDate) {
                            diffTime = Math.abs(latestTransaction - requestDate);
                        }

                        var daysSinceCreation = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        if (daysSinceCreation >= 0 && daysSinceCreation > noTaxDays) {
                            if (noTaxDays) {
                                fees.shift(); // remove zeroTaxFromArray
                            }

                            var taxToApply = 0;
                            var previousLimit = 0;
                            $.each(fees, function(kf, vf) {
                                var feeDayLimit = vf.max_delay_days;

                                if (daysSinceCreation > previousLimit) {
                                    taxToApply = vf.fee_percent;
                                }
                                previousLimit = feeDayLimit;
                            });

                            if (taxToApply) {
                                var extraFeesValue = (v.base_value * parseFloat(taxToApply)) / 100;
                                setTaxValueForArticleRequest(v.id, extraFeesValue)
                            }
                        }
                        // if (latestTransaction >= requestDate) {
                        // } else {
                        //     // something is wrong with the selected date. reset it
                        //     transactionFulfilledInput.val(null);
                        //     latestTransaction = null;
                        //     paymentInfoReady();
                        // }
                    }
                });
            }
        }

        function setTaxValueForArticleRequest(id, value) {
            var request = $.grep(requests, function(n, i) {
                return n.id === id;
            })[0];
            var originalRequest = $.grep(requestsData, function(n, i) {
                return n.id === id;
            })[0];

            var tax = Number.parseFloat(value) <= Number.parseFloat(request.extra_fees_value) ? 0 : value - request
                .extra_fees_value;
            if (multa_emolument.length > 0) {
                let found = multa_emolument.find(element => element == request.id);
                if (found) {
                    request.extra_fees_value = 0;
                    $('#request_tax_' + request.id).text("0,00");
                } else {
                    request.extra_fees_value = tax;
                    $('#request_tax_' + request.id).text("");
                    $('#request_tax_' + request.id).text(request.extra_fees_value.toLocaleString('pt-br', {
                        minimumFractionDigits: 2
                    }));
                }
            } else {
                request.extra_fees_value = value <= request.extra_fees_value ? value : value - request.extra_fees_value;
                $('#request_tax_' + request.id).text("");
                $('#request_tax_' + request.id).text(tax.toLocaleString('pt-br', {
                    minimumFractionDigits: 2
                }));

            }

            if (value !== originalRequest.extra_fees_value) {

                request.balance -= request.extra_fees_value;
            }

            //request.balance = Math.round(request.balance);
            let valorb = parseFloat(request.balance).toFixed(2);
            $('#request_balance_' + request.id).find('span')
                .removeClass(request.balance >= 0 ? 'text-danger' : 'text-success')
                .addClass(request.balance >= 0 ? 'text-success' : 'text-danger')
                .text(valorb);
        }

        function assingValueToArticleRequests() {
            var selectedRequests = $.grep(requests, function(n, i) {
                return $.inArray(n.id, checkedRequestIds) !== -1;
            });

            var valueToAssing = transactionValue + Number.parseFloat(userBalance);

            $.each(selectedRequests, function(k, v) {
                var credit = v.balance + valueToAssing <= 0 ? valueToAssing : valueToAssing - (v.balance +
                    valueToAssing);
                //credit = Math.round(credit);
                valueToAssing -= credit;

                let creditFormatkz = parseFloat(credit).toLocaleString('pt-br', {
                    minimumFractionDigits: 2
                }) + " kz";
                $('#request_to_pay_' + v.id).text(creditFormatkz);

                v.balance += credit;
                //v.balance = Math.round(v.balance);
                let valorb = v.balance.toLocaleString('pt-br', {
                    minimumFractionDigits: 2
                });
                $('#request_balance_' + v.id).find('span')
                    .removeClass(v.balance === 0 ? 'text-danger' : 'text-success')
                    .addClass(v.balance === 0 ? 'text-success' : 'text-danger')
                    .text(valorb + " kz");

                valorPagar += Number.parseFloat((-v.balance))
                Number.parseFloat(valorPagar)
                totalPagar.text(valorPagar.toLocaleString('pt-br', {
                    minimumFractionDigits: 2
                }))



                setArticleRequestState(v.id);
            });
            var saldoCateira = $("#check-saldo").is(":checked") ? valueToAssing + Number.parseFloat(
                '{{ $user->credit_balance }}') : valueToAssing;

            $('#new_user_balance').text(Number.parseFloat(saldoCateira).toFixed(2))
        }

        function setArticleRequestState(id) {
            var request = $.grep(requests, function(n, i) {
                return n.id === id;
            })[0];


            var state = 'error';
            if (request.balance === 0) {
                state = 'total';
                sem_Refencia = true
            } else if (-1 * (request.base_value + request.extra_fees_value) < request.balance && request.balance < 0) {
                state = 'partial';
            } else if (-1 * (request.base_value + request.extra_fees_value) === request.balance) {
                state = 'pending';
                sem_Refencia = true
            }

            $('#request_status_' + request.id).html(statusList[state]);
        }

        function resetRequestsTable() {
            valorPagar = 0
            requests = JSON.parse(JSON.stringify(requestsData));
            $.each(requests, function(k, v) {
                setTaxValueForArticleRequest(v.id, v.extra_fees_value);
                $('#request_to_pay_' + v.id).text(0, 00 + " kz");
                setArticleRequestState(v.id);
            });
        }

        function latestPaymentTransactionDate(transactions) {
            var date = transactionDate;
            $.each(transactions, function(k, v) {
                if (v.type === 'payment') {
                    var fulfilled = new Date(v.transaction_info.fulfilled_at)
                    if (fulfilled > date) {
                        date = fulfilled;
                    }
                }
            })
            return date;
        }

        function clearValueWhenInformationIsClosed() {
            //alert($('#transaction_value').val());
            transactionValue = null;
            paymentInfoReady();

            firstTransactionValue = parseInt($('#transaction_value').val()) > 0 ? parseInt($('#transaction_value').val()) :
                null;
            secondTransactionValue = parseInt($('#transaction_value_1').val()) > 0 ? parseInt($('#transaction_value_1')
                .val()) : null;
            thirdTransactionValue = parseInt($('#transaction_value_2').val()) > 0 ? parseInt($('#transaction_value_2')
                .val()) : null;
            fourthTransactionValue = parseInt($('#transaction_value_3').val()) > 0 ? parseInt($('#transaction_value_3')
                .val()) : null;
            fifthTransactionValue = parseInt($('#transaction_value_4').val()) > 0 ? parseInt($('#transaction_value_4')
                .val()) : null;

            if ($("#group2").is(":hidden")) {
                thirdTransactionValue = 0;
            } else if ($("#group3").is(":hidden")) {
                fourthTransactionValue = 0;
            } else if ($("#group4").is(":hidden")) {
                fifthTransactionValue = 0;
            }

            transactionValue = firstTransactionValue + secondTransactionValue + thirdTransactionValue +
                fourthTransactionValue + fifthTransactionValue;
            $("#total_value").val(null)
            $("#total_value").val("");
            $(".total_value").text("");
            valorPago = Number.parseFloat(transactionValue)
            total_value.text(valorPago.toLocaleString('pt-br', {
                minimumFractionDigits: 2
            }));
            $("#total_value").val(transactionValue);
            paymentInfoReady();
        }

        $(function() {
            //resetTransctionType();
            resetTransaction();
            updateSelectedRequests();
            callPaymentInfo("payment");

            var jabutiImg = null;
            jabutiImg = new Image();
            jabutiImg.onload = function() {
                $(".fotoUserFunc").attr('style', "background-image: url(" + jabutiImg.src + ")")
            }
            jabutiImg.src = "//{{ $_SERVER['HTTP_HOST'] }}/users/avatar/" + '{{ $user->foto }}';


            if (transactionBankInput.val() != "" && userBalance != 0) {
                $("#groupDonePaymentButton").attr('hidden', false)
            } else {

                $("#groupDonePaymentButton").attr('hidden', true)

            }


            var callGroup3 = false;
            var callGroup4 = false;
            var modalWasOpened = 0;
            var referencia_duplicat = false;


            $("#addBank").click(function() {

                if ($("#group3").is(":visible")) {
                    $("#group4").prop('hidden', false);
                    $("#addBank").prop('hidden', true)
                }

                if (callGroup3) {
                    $("#group3").prop('hidden', false);
                }

                if ($("#group1").is(":visible")) {
                    $("#group2").prop('hidden', false);
                    callGroup3 = true;
                    $("#transaction_value_2").prop('required', true);
                    $("#transaction_value_2").prop('disabled', false);
                    $("#date_2").prop('required', true);
                    $("#bank_2").prop('required', true);
                    $("#reference_2").prop('required', true);
                    $("#group1").prop('hidden', false);
                    $("#transaction_value_1").prop('required', true);
                    $("#date_1").prop('required', true);
                    $("#bank_1").prop('required', true);
                    $("#reference_1").prop('required', true);

                } else if ($("#group1").is(":hidden")) {
                    $("#group1").prop('hidden', false);
                    $("#transaction_value_1").prop('required', true);
                    $("#transaction_value_1").prop('disabled', false);
                    $("#date_1").prop('required', true);
                    $("#bank_1").prop('required', true);
                    $("#reference_1").prop('required', true);
                    $("#removeBank").prop('hidden', false)

                }

            });

            $("#removeBank").click(function() {
                if ($("#group1").is(":visible") && $("#group2").is(":visible") && $("#group3").is(
                        ":hidden") && $("#group4").is(":hidden")) {
                    $("#group2").prop('hidden', true);

                    $("#transaction_value_2").val('');
                    $("#date_2").val('');
                    $("#reference_2").val('');

                    $("#transaction_value_2").prop('disabled', true);
                    $("#date_2").prop('disabled', true);
                    $("#bank_2").prop('disabled', true);
                    $("#reference_2").prop('disabled', true);

                    clearValueWhenInformationIsClosed();

                    $("#addBank").prop('hidden', false);
                } else if ($("#group1").is(":visible") && $("#group2").is(":visible") && $("#group3").is(
                        ":visible") && $("#group4").is(":hidden")) {

                    $("#group3").prop('hidden', true);
                    callGroup3 = false;

                    $("#transaction_value_3").val('');
                    $("#date_3").val('');
                    $("#reference_3").val('');

                    clearValueWhenInformationIsClosed();
                    $("#addBank").prop('hidden', false);

                } else if ($("#group1").is(":visible") && $("#group2").is(":visible") && $("#group3").is(
                        ":visible") && $("#group4").is(":visible")) {
                    $("#group4").prop('hidden', true);

                    $("#transaction_value_4").val('');
                    $("#date_4").val('');
                    $("#reference_4").val('');

                    $("#addBank").prop('hidden', false);

                    clearValueWhenInformationIsClosed();
                } else if ($("#group1").is(":visible") && $("#group2").is(":hidden") && $("#group3").is(
                        ":hidden") && $("#group4").is(":hidden")) {
                    $("#group1").prop('hidden', true);
                    $("#transaction_value_1").prop('disabled', true);
                    $("#date_1").prop('disabled', true);
                    $("#bank_1").prop('disabled', true);
                    $("#reference_1").prop('disabled', true);

                    $("#transaction_value_1").val('');
                    $("#date_1").val('');
                    $("#reference_1").val('');

                    clearValueWhenInformationIsClosed();


                    $("#removeBank").prop('hidden', true);
                }
            });

            $('body').on('change', '#transaction_type', function() {
                resetTransaction();

                transactionType = this.value;

                if (transactionType === 'payment') {
                    enableBaseTransaction();
                    enableTransactionInfo();
                }

                if (transactionType === 'adjust') {
                    enableBaseTransaction();
                }

                paymentInfoReady();
            });

            function callPaymentInfo(type) {
                resetTransaction();
                if (type === "payment") {
                    enableBaseTransaction();
                    enableTransactionInfo();
                }

                if (type === "adjust") {
                    enableBaseTransaction();
                }

                paymentInfoReady();
            }



            $('#transaction_types').change(function() {
                callPaymentInfo(this.value);
            });
            transactionBankInput.on('change', function() {
                if (transactionBankInput.val() != "") {

                    if (transactionFulfilledInput.val() != "" && transactionValueInput.val() != "" &&
                        transactionRefInput.val() != "") {
                        $("#groupDonePaymentButton").attr('hidden', false)

                    } else {
                        $("#groupDonePaymentButton").attr('hidden', true)

                    }
                } else {

                    $("#groupDonePaymentButton").attr('hidden', true)
                }

            })


            transactionValueInput.on('keypress', function() {
                if (transaction_types.val() != "adjust" && transactionValueInput.val() != "") {

                    if (transactionFulfilledInput.val() != "" && transactionBankInput.val() != "" &&
                        transactionRefInput.val() != "") {
                        $("#groupDonePaymentButton").attr('hidden', false)

                    } else {
                        $("#groupDonePaymentButton").attr('hidden', true)

                    }
                }
                if (transaction_types.val() === "adjust" && transactionValueInput.val() != "" &&
                    transactionFulfilledInput.val() != "") {
                    $("#groupDonePaymentButton").attr('hidden', false)
                    console.log(transaction_types.val())
                } else {

                    $("#groupDonePaymentButton").attr('hidden', true)
                }

                // if (userBalance==0 && transactionFulfilledInput.val()!="" &&  transactionBankInput.val()!="" && transactionRefInput.val()!="" ) {
                //     $("#groupDonePaymentButton").attr('hidden',false)
                // } else {
                //     $("#groupDonePaymentButton").attr('hidden',true)
                // }
            });

            transactionRefInput.bind('keypress keydown keyup', function() {
                if (transactionRefInput.val() != "") {
                    if (transactionValueInput.val() != "" && transactionBankInput.val() != "" &&
                        transactionFulfilledInput.val() != "") {
                        $("#groupDonePaymentButton").attr('hidden', false)
                    } else {
                        $("#groupDonePaymentButton").attr('hidden', true)

                    }
                } else {
                    $("#groupDonePaymentButton").attr('hidden', true)
                }
            })




            transactionValueInput.on('change', function() {
                firstTransactionValue = parseFloat(this.value) > 0 ? parseFloat(this.value) : null;

                transactionValue = null;

                if ($("#group1").is(":hidden")) {
                    secondTransactionValue = 0;
                } else if ($("#group1").is(":hidden") && $("#group2").is(":hidden")) {
                    secondTransactionValue = 0;
                    thirdTransactionValue = 0;
                    fourthTransactionValue = 0;
                    fifthTransactionValue = 0;
                }
                if (transactionValueInput.val() != "") {

                    if (transactionFulfilledInput.val() != "" && transactionBankInput.val() != "" &&
                        transactionRefInput.val() != "") {
                        $("#groupDonePaymentButton").attr('hidden', false)

                    } else {
                        $("#groupDonePaymentButton").attr('hidden', true)

                    }
                } else {

                    $("#groupDonePaymentButton").attr('hidden', true)
                }

                // if (userBalance==0 && transactionFulfilledInput.val()!="" &&  transactionBankInput.val()!="" && transactionRefInput.val()!="" ) {
                //   $("#groupDonePaymentButton").attr('hidden',false)
                // } else {
                //      $("#groupDonePaymentButton").attr('hidden',true)
                // }

                transactionValue = firstTransactionValue + secondTransactionValue + thirdTransactionValue +
                    fourthTransactionValue + fifthTransactionValue;
                $("#total_value").val("");
                $(".total_value").text("");
                $("#total_value").val(transactionValue);
                valorPago = Number.parseFloat(transactionValue)
                total_value.text(valorPago.toLocaleString('pt-br', {
                    minimumFractionDigits: 2
                }))
                paymentInfoReady()
            });

            firstTransactionValueInput.on('change', function() {
                secondTransactionValue = parseFloat(this.value) > 0 ? parseFloat(this.value) : null;
                transactionValue = null;

                if ($("#group2").is(":hidden")) {
                    thirdTransactionValue = 0;
                    fourthTransactionValue = 0;
                    fifthTransactionValue = 0;
                }
                transactionValue = secondTransactionValue + firstTransactionValue + thirdTransactionValue +
                    fourthTransactionValue + fifthTransactionValue;
                $("#total_value").val(transactionValue);
                valorPago = Number.parseFloat(transactionValue)
                total_value.text(valorPago.toLocaleString('pt-br', {
                    minimumFractionDigits: 2
                }))
                paymentInfoReady()
            });

            secondTransactionValueInput.on('change', function() {
                thirdTransactionValue = parseFloat(this.value) > 0 ? parseFloat(this.value) : null;
                transactionValue = null;

                if ($("#group3").is(":hidden")) {
                    fourthTransactionValue = 0;
                    fifthTransactionValue = 0;
                }
                transactionValue = fourthTransactionValue + thirdTransactionValue + secondTransactionValue +
                    firstTransactionValue + fifthTransactionValue;
                $("#total_value").val(transactionValue);
                valorPago = Number.parseFloat(transactionValue)
                total_value.text(valorPago.toLocaleString('pt-br', {
                    minimumFractionDigits: 2
                }))
                paymentInfoReady()
            });

            thirdTransactionValueInput.on('change', function() {
                fourthTransactionValue = parseFloat(this.value) > 0 ? parseFloat(this.value) : null;
                transactionValue = null;

                if ($("#group4").is(":hidden")) {
                    fifthTransactionValue = 0;
                }

                transactionValue = thirdTransactionValue + secondTransactionValue + firstTransactionValue +
                    fourthTransactionValue + fifthTransactionValue;
                $("#total_value").val(transactionValue);
                valorPago = Number.parseFloat(transactionValue)
                total_value.text(valorPago.toLocaleString('pt-br', {
                    minimumFractionDigits: 2
                }))
                paymentInfoReady()
            });

            fourthTransactionValueInput.on('change', function() {
                fifthTransactionValue = parseFloat(this.value) > 0 ? parseFloat(this.value) : null;
                transactionValue = null;
                transactionValue = thirdTransactionValue + secondTransactionValue + firstTransactionValue +
                    fourthTransactionValue + fifthTransactionValue;
                $("#total_value").val(transactionValue);
                valorPago = Number.parseFloat(transactionValue)
                total_value.text(valorPago.toLocaleString('pt-br', {
                    minimumFractionDigits: 2
                }))
                paymentInfoReady()
            });

            // trabalhar as data da transação, os emolumentos (PROPINA) devem pegar qualquer data (neste vamos dar atenção),
            // a maoir data do transacao, para verficar se o emolumento encontra-se dentro da data da transacção.

            var dateObj = [];

            var data_1 = 0;
            var data_2 = 0;
            var data_3 = 0;
            var data_4 = 0;

            $('input[type=checkbox]').on('change', function() {
                var month = $(this).attr("data-id");
                var id_artRequest = $(this).attr("data-columns");

                if ($("#checados" + id_artRequest).is(":checked")) {
                    multa_emolument.push(id_artRequest)
                    multa_emolument.sort()
                    paymentInfoReady()
                } else {
                    $.each(multa_emolument, function(index, element) {
                        if (element == id_artRequest) {
                            multa_emolument.splice([index], 1)
                        }
                    });
                    paymentInfoReady()
                }
            })

            $('input[type=date]').on('change', function() {
                var dataAtual = this.value ? new Date(this.value) : null;

                var data_1 = transaction_fulfilled_at_1ValueInput.val() == undefined ? 0 :
                    transaction_fulfilled_at_1ValueInput.val();
                var data_2 = transaction_fulfilled_at_2ValueInput.val() == undefined ? 0 :
                    transaction_fulfilled_at_2ValueInput.val();
                var data_3 = transaction_fulfilled_at_3ValueInput.val() == undefined ? 0 :
                    transaction_fulfilled_at_3ValueInput.val();
                var data_4 = transaction_fulfilled_at_4ValueInput.val() == undefined ? 0 :
                    transaction_fulfilled_at_4ValueInput.val();


                dateObj = [transactionFulfilledInput.val(), data_1, data_2, data_3, data_4]
                dateObj.sort()
                dateObj.reverse()

                transactionDate = this.value != 0 ? new Date(dateObj[0]) : null;
                var now = new Date();

                if (dataAtual > now) {
                    dataAtual = now;
                    $(this).val(dataAtual.toISOString().slice(0, 10))
                } else {
                    paymentInfoReady()
                }

                if (transaction_types.val() != "adjust" && transactionFulfilledInput.val() != "") {
                    if (transactionValueInput.val() != "" && transactionBankInput.val() != "" &&
                        transactionRefInput.val() != "") {
                        $("#groupDonePaymentButton").attr('hidden', false)
                    } else {

                        $("#groupDonePaymentButton").attr('hidden', true)

                    }
                }
                if (transaction_types.val() === "adjust" && transactionValueInput.val() != "" &&
                    transactionFulfilledInput.val() != "") {
                    $("#groupDonePaymentButton").attr('hidden', false)
                } else {
                    $("#groupDonePaymentButton").attr('hidden', true)
                }

                if (userBalance != 0) {
                    if (transaction_types.val() != "adjust" && transactionValueInput.val() != "" &&
                        transactionBankInput.val() != "" && transactionRefInput.val() != "") {
                        $("#groupDonePaymentButton").attr('hidden', false)
                    }
                    if (transaction_types.val() === "adjust" && transactionValueInput.val() != "" &&
                        transactionFulfilledInput.val() != "") {
                        $("#groupDonePaymentButton").attr('hidden', false)
                    } else {
                        if (transaction_types.val() != "adjust" && transactionValueInput.val() == "" &&
                            transactionBankInput.val() == "" && transactionRefInput.val() == "") {
                            $("#groupDonePaymentButton").attr('hidden', false)

                        } else {
                            $("#groupDonePaymentButton").attr('hidden', true)

                        }
                    }
                }

            });

            $("#date_1").on('change', function() {

            });

            $("#data_2").on('change', function() {

            });




            $('input[name="transaction_reference"]').on('blur', function() {
                var referecia = $(this).val()
                referencia_1 = referecia
                referencia_1 != "" ? referencia_1 != referencia_2 && referencia_1 != referencia_3 &&
                    referencia_1 != referencia_4 && referencia_1 != referencia_5 ? referencia_duplicat =
                    false : referencia_duplicat = true : 0;
                if (referencia_duplicat != false) {
                    alert("está referencia duplicada por favor digite outra referência !")
                } else {

                    Forlearn.checkIfModelFieldExists(this, '{{ route('transactions.reference_exists') }}')
                    setTimeout(() => {
                        if ($('input[name="transaction_reference"]').hasClass("is-invalid")) {
                            ref1 = false;
                            if (sem_Refencia == true) {
                                $("#groupDonePaymentButton").prop('hidden', true);
                                $("#transaction_reference").prop('required', false);
                                $("#transaction_bank").prop('required', false);
                            } else {
                                $("#groupDonePaymentButton").prop('hidden', false);
                                $("#transaction_reference").prop('required', true);
                                $("#transaction_bank").prop('required', true);

                            }
                            getInfoReferences($('input[name="transaction_reference"]'));
                        } else {
                            ref1 = true;
                            if (ref2 == true && ref3 == true && ativo_month == true) {
                                $("#groupDonePaymentButton").prop('hidden', false);
                            } else if (sem_Refencia == true) {
                                $("#groupDonePaymentButton").prop('hidden', false);
                            }
                        }

                        if (transactionRefInput.val() != "") {
                            if (transactionValueInput.val() != "" && transactionBankInput.val() !=
                                "" && transactionFulfilledInput.val() != "") {
                                $("#groupDonePaymentButton").attr('hidden', false)
                            } else {
                                $("#groupDonePaymentButton").attr('hidden', true)

                            }
                        } else {
                            $("#groupDonePaymentButton").attr('hidden', true)
                        }
                        if (ref1 == false) {
                            $("#groupDonePaymentButton").prop('hidden', true);
                        }
                    }, 1000);

                    // transactionRefInput
                    if (transactionRefInput.val() != "") {
                        if (transactionValueInput.val() != "" && transactionBankInput.val() != "" &&
                            transactionFulfilledInput.val() != "") {
                            $("#groupDonePaymentButton").attr('hidden', false)
                        } else {
                            $("#groupDonePaymentButton").attr('hidden', true)

                        }
                    } else {
                        $("#groupDonePaymentButton").attr('hidden', true)
                    }

                }
            });

            $('input[name="reference_1"]').on('blur', function() {
                var referecia = $(this).val()
                referencia_2 = referecia
                referencia_2 != "" ? referencia_2 != referencia_1 && referencia_2 != referencia_3 &&
                    referencia_2 != referencia_4 && referencia_2 != referencia_5 ? referencia_duplicat =
                    false : referencia_duplicat = true : 0;

                if (referencia_duplicat != false) {
                    alert("Referencia duplicada por favor digite outra referência !")
                    $("#groupDonePaymentButton").prop('hidden', true);
                } else {
                    Forlearn.checkIfModelFieldExists(this, '{{ route('transactions.reference1_exists') }}')

                    setTimeout(() => {
                        if ($('input[name="reference_1"]').hasClass("is-invalid")) {
                            ref2 = false;
                            $("#groupDonePaymentButton").prop('hidden', true);
                            getInfoReferences($('input[name="reference_1"]'));
                        } else {
                            ref2 = true;
                            if (ref1 == true && ref3 == true && ativo_month == true) {
                                $("#groupDonePaymentButton").prop('hidden', false);
                            }
                        }
                        if (ref2 == false) {
                            $("#groupDonePaymentButton").prop('hidden', true);
                        }
                    }, 1000);
                }

            });

            $('input[name="reference_2"]').on('blur', function() {
                var referecia = $(this).val()
                referencia_3 = referecia
                referencia_3 != "" ? referencia_3 != referencia_1 && referencia_3 != referencia_2 &&
                    referencia_3 != referencia_4 && referencia_3 != referencia_5 ? referencia_duplicat =
                    false : referencia_duplicat = true : 0;

                if (referencia_duplicat != false) {
                    alert("Referencia duplicada por favor digite outra referência !")
                } else {
                    Forlearn.checkIfModelFieldExists(this, '{{ route('transactions.reference2_exists') }}')

                    setTimeout(() => {
                        if ($('input[name="reference_2"]').hasClass("is-invalid")) {
                            ref3 = false;
                            $("#groupDonePaymentButton").prop('hidden', true);
                            getInfoReferences($('input[name="reference_2"]'));
                        } else {
                            ref3 = true;
                            if (ref1 == true && ref2 == true && ativo_month == true) {
                                $("#groupDonePaymentButton").prop('hidden', false);
                            }
                        }
                        if (ref3 == false) {
                            $("#groupDonePaymentButton").prop('hidden', true);
                        }
                    }, 1000);
                }

            });

            $('input[name="reference_3"]').on('blur', function() {
                var referecia = $(this).val()
                referencia_4 = referecia
                referencia_4 != "" ? referencia_4 != referencia_1 && referencia_4 != referencia_2 &&
                    referencia_4 != referencia_3 && referencia_4 != referencia_5 ? referencia_duplicat =
                    false : referencia_duplicat = true : 0;

                if (referencia_duplicat != false) {
                    alert("Referencia duplicada por favor digite outra referência !")
                } else {
                    Forlearn.checkIfModelFieldExists(this, '{{ route('transactions.reference3_exists') }}')

                    setTimeout(() => {
                        if ($('input[name="reference_3"]').hasClass("is-invalid")) {
                            ref4 = false;
                            $("#groupDonePaymentButton").prop('hidden', true);
                            getInfoReferences($('input[name="reference_3"]'));
                        } else {
                            ref4 = true;
                            if (ref1 == true && ref2 == true && ref3 == true && ativo_month ==
                                true) {
                                $("#groupDonePaymentButton").prop('hidden', false);
                            }
                        }
                        if (ref4 == false) {
                            $("#groupDonePaymentButton").prop('hidden', true);
                        }
                    }, 1000);
                }

            });

            $('input[name="reference_4"]').on('blur', function() {
                var referecia = $(this).val()
                referencia_5 = referecia

                referencia_5 != "" ? referencia_5 != referencia_1 && referencia_5 != referencia_2 &&
                    referencia_5 != referencia_3 && referencia_5 != referencia_4 ? referencia_duplicat =
                    false : referencia_duplicat = true : 0;

                if (referencia_duplicat != false) {
                    alert("Referencia duplicada por favor digite outra referência !")
                } else {
                    Forlearn.checkIfModelFieldExists(this, '{{ route('transactions.reference4_exists') }}')

                    setTimeout(() => {
                        if ($('input[name="reference_4"]').hasClass("is-invalid")) {
                            ref5 = false;
                            $("#groupDonePaymentButton").prop('hidden', true);
                            getInfoReferences($('input[name="reference_4"]'));
                        } else {
                            ref5 = true;
                            if (ref1 == true && ref2 == true && ref3 == true && ref4 == true &&
                                ativo_month == true) {
                                $("#groupDonePaymentButton").prop('hidden', false);
                            }
                        }

                        if (ref5 == false) {
                            $("#groupDonePaymentButton").prop('hidden', true);
                        }
                    }, 1000);
                }

            });

            $(".referencia-config").keyup(function() {
                var texto = $(this).val();
                texto = texto.replace('  ', ' ');
                $(this).val(texto);
                if (texto[0] == " ") {
                    texto = texto.slice(1);
                    $(this).val(texto);
                }
            });

            $(".referencia-config").on("blur", function() {
                var texto = $(this).val();
                if (texto[texto.length - 1] == " ") {
                    $(this).val(texto.slice(0, texto.length - 1));
                }
            });
            $(".referencia-config").on("keypress", function() {
                $(this).val($(this).val().replace('  ', ' '));
            });

            $(window).keydown(function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });
            $("#transaction_value").keyup(function(e) {
                var valor = transactionValueInput.val();
                var er = /[^0-9.]/;
                er.lastIndex = 0;
                if (valor == '') {
                    $("#valor1").text("")
                } else {
                    if (er.test(transactionValueInput.val())) {
                        transactionValueInput.val("");
                    } else {
                        valor = Number.parseFloat(transactionValueInput.val())
                        $("#valor1").html(valor.toLocaleString('pt-br', {
                            minimumFractionDigits: 2
                        }) + " <span>Kz</span>")
                    }

                }
            });
            $("#transaction_value_1").keyup(function(e) {
                var valor = firstTransactionValueInput.val();
                var er = /[^0-9.]/;
                er.lastIndex = 0;
                if (valor == '') {
                    $("#valor2").text("")
                } else {
                    if (er.test(firstTransactionValueInput.val())) {
                        firstTransactionValueInput.val("");
                    } else {
                        valor = Number.parseFloat(firstTransactionValueInput.val())
                        $("#valor2").html(valor.toLocaleString('pt-br', {
                            minimumFractionDigits: 2
                        }) + " <span>Kz</span>")
                    }

                }
            });
            $("#transaction_value_2").keyup(function(e) {
                var valor = secondTransactionValueInput.val();
                var er = /[^0-9.]/;
                er.lastIndex = 0;
                if (valor == '') {
                    $("#valor3").text("")
                } else {
                    if (er.test(secondTransactionValueInput.val())) {
                        secondTransactionValueInput.val("");
                    } else {
                        valor = Number.parseFloat(secondTransactionValueInput.val())
                        $("#valor3").html(valor.toLocaleString('pt-br', {
                            minimumFractionDigits: 2
                        }) + " <span>Kz</span>")
                    }

                }
            });
            $("#transaction_value_3").keyup(function(e) {
                var valor = thirdTransactionValueInput.val();
                var er = /[^0-9.]/;
                er.lastIndex = 0;
                if (valor == '') {
                    $("#valor4").text("")
                } else {
                    if (er.test(thirdTransactionValueInput.val())) {
                        thirdTransactionValueInput.val("");
                    } else {
                        valor = Number.parseFloat(thirdTransactionValueInput.val())
                        $("#valor4").html(valor.toLocaleString('pt-br', {
                            minimumFractionDigits: 2
                        }) + " <span>Kz</span>")
                    }

                }
            });
            $("#transaction_value_4").keyup(function(e) {
                var valor = fourthTransactionValueInput.val();
                var er = /[^0-9.]/;
                er.lastIndex = 0;
                if (valor == '') {
                    $("#valor5").text("")
                } else {
                    if (er.test(fourthTransactionValueInput.val())) {
                        fourthTransactionValueInput.val("");
                    } else {
                        valor = Number.parseFloat(fourthTransactionValueInput.val())
                        $("#valor5").html(valor.toLocaleString('pt-br', {
                            minimumFractionDigits: 2
                        }) + " <span>Kz</span>")
                    }

                }
            });

            function getInfoReferences(reference) {


                $.ajax({
                    method: "GET",
                    url: "reference_get_origem/" + reference.val()
                }).done(function(info) {

                    $(".modal_Referencia").modal('show');

                    $(".modal_Referencia .btn-get-reference").attr('href', "requests/" + info["info"]
                        .article_request_id);

                    const nomeArquivo = (info["info"].recibo).split('/').pop();
                    const urlRecibo = "https://ispk.forlearn.ao/payments/receipts/" + nomeArquivo;
                    $(".modal_Referencia .btn-get-recibo").attr('href',
                        urlRecibo);

                    $(".modal_Referencia #texto-reference").html("Caro " + info["nome"] +
                        ", forLEARN detectou a Referência:<span class='text-warning' style='font-size: 23px;'> " +
                        info["info"].referencia +
                        "</span> no recibo Nº " + info["recibo"] + "");


                });

            }

        });
    </script>
@endsection
