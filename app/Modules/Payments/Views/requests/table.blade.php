{{-- Avaliar o primeiro item do pago usando o first->loop do foreach --}}
{{-- {{$modelo}} --}}
<style>
    .divtable::-webkit-scrollbar {
        width: 8px;
        height: 2px;
        border-radius: 30px;
        box-shadow: inset 20px 20px 60px #bebebe,
            inset -20px -20px 60px #ffffff;
    }

    .divtable::-webkit-scrollbar-track {
        background: #e0e0e0;
        box-shadow: inset 20px 20px 60px #bebebe,
            inset -20px -20px 60px #ffffff;
        border-radius: 30px;
        height: 2px
    }

    .divtable::-webkit-scrollbar-thumb {
        background-color: #343a40;
        border-radius: 30px;
        border: none;
        height: 2px
    }

    .divtable {
        height: 50vh
    }
</style>
<div style="background: #00d55a;padding: 3px ; border-top-left-radius: 1pc ;border-top-right-radius: 1pc"
    class="div-borda"></div>
<div class="divtable table-responsive mt-2">
    @php $total_to_pay = 0; @endphp
    
    @if (!auth()->check() && !auth()->user()->hasRole('admin'))
        <h1>erro</h1>
    @endif

    <table style="z-index: 1;" id="requests-trans-table" class="table table-striped table-hover table-tesoraria">
        <thead>
            <th>#</th>

            @if (auth()->user()->hasRole('chefe_tesoureiro') ||
                    auth()->user()->hasRole('tesoureiro') ||
                    auth()->user()->hasRole('superadmin') ||
                    auth()->user()->hasRole('promotor') ||
                    auth()->user()->hasRole('presidente'))
                <th class="checkbox-tesoraria"><i class="fa fa-check-square"></i></th>
            @endif
            <th>Emolumento / Propina</th>
            <th>Valor</th>
            <th>Multa</th>
            <th>Pagamento</th>
            <th>Factura/Recibo nº</th>
            <th class="accoes-tesoraria">Acções</th>
        </thead>
        <tbody>
            @php
                $verificar_model = count($model);
                $verificar_disciplines = count($disciplines);
            @endphp
            @php $count = 1; @endphp
            @php $i = 1; @endphp
            @php $repet=1; @endphp
            @php $repetMonth=0; @endphp
            @php $repetTaixa=1; @endphp
            @php $repetTaixaArti_request=null; @endphp
            @php $explode=null; @endphp
            @php $expl=null; @endphp
            @php $vetorArticl=[]; @endphp
            @php $artclAtivo=null; @endphp
            @php $article_code=null; @endphp

            @if ($verificar_model > 0 || $verificar_disciplines > 0)
                @foreach ($model as $articles)
                    @if (empty($vetorArticl))
                        @php
                            $vetorArticl[] = $articles->article_req_id;
                            $artclAtivo = true;
                            $article_code = $articles->code;
                        @endphp
                    @else
                        @if (in_array($articles->article_req_id, $vetorArticl) && $articles->code == $article_code)
                            @php $artclAtivo=false; @endphp
                        @else
                            @if (in_array($articles->article_req_id, $vetorArticl) && $articles->status == 'pending')
                                @php
                                    $article_code = $articles->code;
                                    $vetorArticl[] = $articles->article_req_id;
                                    $artclAtivo = false;
                                @endphp
                            @else
                                @php
                                    $article_code = $articles->code;
                                    $vetorArticl[] = $articles->article_req_id;
                                    $artclAtivo = true;
                                @endphp
                            @endif
                        @endif
                    @endif
                    @if ($artclAtivo == true)
                        @if ($articles->status == 'total' && $articles->data_from == 'Estorno')
                        @else
                            <tr>
                                <td>{{ $count++ }} </td>

                                @if ($articles->status == 'total')
                                    @if (auth()->user()->hasRole('chefe_tesoureiro') ||
                                            auth()->user()->hasRole('tesoureiro') ||
                                            auth()->user()->hasRole('superadmin') ||
                                            auth()->user()->hasRole('promotor') ||
                                            auth()->user()->hasRole('presidente'))
                                        <td class="checkbox-tesoraria c-1"></td>
                                    @endif
                                @elseif($articles->status == 'pending' || $articles->status == 'partial')
                                    @if (auth()->user()->hasRole('chefe_tesoureiro') ||
                                            auth()->user()->hasRole('tesoureiro') ||
                                            auth()->user()->hasRole('superadmin') ||
                                            auth()->user()->hasRole('promotor') ||
                                            auth()->user()->hasRole('presidente'))
                                        @php $i =$count-1; @endphp
                                        <td class="checkbox-tesoraria c-2">
                                            @if ($articles->art_idDisciplina == '' || $articles->article_req_id)
                                                <input data-one="1"
                                                    data-idTransacion="{{ $articles->transaction_id }}"
                                                    data-status="{{ $articles->status }}"
                                                    data-year="{{ $articles->article_year }}"
                                                    data-columns="{{ $i }}"
                                                    id="checagem{{ $articles->article_month }}"
                                                    data-id="{{ $articles->article_month }}" type="checkbox"
                                                    class="checagem_month checagemdeleteArticl btn-checked "
                                                    name="checked_values[]" value="{{ $articles->article_req_id }}">
                                            @else
                                                <input data-two="2"
                                                    data-idTransacion="{{ $articles->transaction_id }}"
                                                    data-status="{{ $articles->status }}"
                                                    data-columns="{{ $i }}" id="checagem" data-id=""
                                                    type="checkbox" class="checagem checagemdeleteArticl btn-checked "
                                                    name="checked_values[]" value="{{ $articles->article_req_id }}">
                                            @endif
                                        </td>
                                    @else
                                        {{-- <td class="checkbox-tesoraria"></td> --}}
                                    @endif
                                @else
                                    <td class="checkbox-tesoraria c-3"></td>
                                @endif
                                <td>
                                    {{ $articles->article_name }}
                                    @foreach ($disciplines as $discipline)
                                        @if ($discipline->article_req_id == $articles->article_req_id)
                                            @if ($discipline->discipline_id != null)
                                                ({{ '#' . $discipline->codigo_disciplina . ' - ' . $discipline->discipline_name }})
                                            @endif
                                        @endif
                                    @endforeach
                                    @foreach ($metrics as $metric)
                                        @if ($metric->article_req_id == $articles->article_req_id)
                                            @if ($metric->metric_id != null)
                                                ({{ $metric->nome }})
                                            @endif
                                        @endif
                                    @endforeach
                                    @if ($articles->article_month == 1)
                                        ( Janeiro {{ $articles->article_year }} )
                                    @elseif($articles->article_month == 2)
                                        ( Fevereiro {{ $articles->article_year }} )
                                    @elseif ($articles->article_month == 3)
                                        ( Março {{ $articles->article_year }} )
                                    @elseif ($articles->article_month == 4)
                                        ( Abril {{ $articles->article_year }} )
                                    @elseif ($articles->article_month == 5)
                                        ( Maio {{ $articles->article_year }} )
                                    @elseif ($articles->article_month == 6)
                                        ( Junho {{ $articles->article_year }} )
                                    @elseif ($articles->article_month == 7)
                                        ( Julho {{ $articles->article_year }} )
                                    @elseif ($articles->article_month == 8)
                                        ( Agosto {{ $articles->article_year }} )
                                    @elseif ($articles->article_month == 9)
                                        ( Setembro {{ $articles->article_year }} )
                                    @elseif ($articles->article_month == 10)
                                        ( Outubro {{ $articles->article_year }} )
                                    @elseif ($articles->article_month == 11)
                                        ( Novembro {{ $articles->article_year }} )
                                    @elseif ($articles->article_month == 12)
                                        ( Dezembro {{ $articles->article_year }} )
                                    @endif
                                </td>
                                {{-- <td>
                                                                    @foreach ($disciplines as $discipline)
                                                                        @if ($discipline->article_req_id == $articles->article_req_id)
                                                                        @if ($discipline->discipline_id != null)
                                                                            {{ $discipline->discipline_name }}
                                                                        @endif
                                                                        @endif
                                                                    @endforeach
                                                                </td> --}}
                                <td>
                                    {{-- {{$articles->trans_type}} - {{$articles->code}} -  {{$articles->article_req_id}} -  {{$articles->data_from}}- {{$articles->transaction_id}} -   {{$articles->status}} -  --}}

                                    @if (count($getRegraImplementada) > 0)
                                        @if (in_array($articles->article_month, $arrayMonth_getRegraImplementada))
                                            @foreach ($getRegraImplementada as $item)
                                                @if ($articles->art_idDisciplina === '' && $articles->article_year != null && $articles->article_month == $item->mes)
                                                    <?php echo number_format($item->valor ?: 0, 2, ',', ' '); ?> <small>Kz</small>&nbsp; - &nbsp;<s>
                                                        <?php echo number_format($articles->base_value ?: 0, 0, ',', ' '); ?> <small>Kz</small></s>
                                                @endif
                                            @endforeach
                                        @else
                                            @if ($articles->art_idDisciplina == '' && $articles->article_year != null && $articles->article_month != null)
                                                <?php echo number_format($articles->base_value ?: 0, 2, ',', ' '); ?> <small>Kz</small>
                                            @endif
                                        @endif
                                        @if ($articles->article_year == null && $articles->article_month == null)
                                            <?php echo number_format($articles->base_value ?: 0, 2, ',', ' '); ?> <small>Kz</small>
                                        @endif
                                        @if ($articles->art_idDisciplina != '' && $articles->article_year != null && $articles->article_month != null)
                                            <?php echo number_format($articles->base_value ?: 0, 2, ',', ' '); ?> <small>Kz</small>
                                        @endif
                                        @if ($articles->art_idDisciplina != '' && $articles->article_year == null && $articles->article_month == null)
                                        @endif
                                    @elseif (count($getRegraImplementEmolu) > 0 && empty($regraImplementada))
                                        @if (in_array($articles->article_month, $arrayMonth_getRegraImplementEmolu))
                                            @foreach ($getRegraImplementEmolu as $item)
                                                @if ($articles->art_idDisciplina === '' && $articles->article_year != null && $articles->article_month == $item->mes)
                                                    <?php echo number_format($item->valor ?: 0, 2, ',', ' '); ?> <small>Kz</small>&nbsp; - &nbsp;<s>
                                                        <?php echo number_format($articles->base_value ?: 0, 0, ',', ' '); ?> <small>Kz</small></s>
                                                @endif
                                            @endforeach
                                        @else
                                            @if ($articles->art_idDisciplina == '' && $articles->article_year != null && $articles->article_month != null)
                                                <?php echo number_format($articles->base_value ?: 0, 2, ',', ' '); ?> <small>Kz</small>
                                            @endif
                                        @endif

                                        @if ($articles->article_year == null && $articles->article_month == null)
                                            <?php echo number_format($articles->base_value ?: 0, 2, ',', ' '); ?> <small>Kz</small>
                                        @endif
                                        @if ($articles->art_idDisciplina != '' && $articles->article_year != null && $articles->article_month != null)
                                            <?php echo number_format($articles->base_value ?: 0, 2, ',', ' '); ?> <small>Kz</small>
                                        @endif
                                        @if ($articles->art_idDisciplina != '' && $articles->article_year == null && $articles->article_month == null)
                                        @endif
                                    @else
                                        <?php echo number_format($articles->base_value ?: 0, 2, ',', ' '); ?> <small>Kz</small>
                                    @endif



                                </td>
                                <td>

                                    {{-- {{ $articles->extra_fees_value }} --}}
                                    @if ($repetTaixa == 1)
                                        @php $repetTaixa=$articles->extra_fees_value ; @endphp
                                        @php $repetTaixaArti_request=$articles->article_req_id ; @endphp
                                        {{ $articles->extra_fees_value }} <small>Kz</small>
                                    @elseif($repetTaixa != $articles->extra_fees_value && $repetTaixaArti_request != $articles->article_req_id)
                                        @php $repetTaixa=$articles->extra_fees_value ; @endphp
                                        @php $repetTaixaArti_request=$articles->article_req_id ; @endphp
                                        {{ $articles->extra_fees_value }} <small>Kz</small>
                                    @elseif($repetTaixa == $articles->extra_fees_value && $repetTaixaArti_request != $articles->article_req_id)
                                        @php $repetTaixa=$articles->extra_fees_value ; @endphp
                                        @php $repetTaixaArti_request=$articles->article_req_id ; @endphp
                                        @if ($articles->status == 'pending')
                                            {{ $articles->extra_fees_value }} <small>Kz</small>
                                        @else
                                            {{ $articles->extra_fees_value }} <small>Kz</small>
                                        @endif
                                    @else
                                        0 <small>Kz</small>
                                    @endif


                                </td>
                                @php $saldo= $articles->valor_credit !='' ?  $articles->valor_credit :  0 @endphp

                                @if ($articles->status == 'pending' && $articles->extra_fees_value == 0)
                                    @php $total_to_pay = $total_to_pay + $articles->base_value @endphp
                                    <td> <span class="bg-info p-1" id="status_{{ $articles->article_month }}">ESPERA
                                        </span> </td>
                                @elseif($articles->status == 'total')
                                    <td><span class="bg-success p-1 text-white"
                                            id="status_{{ $articles->article_month }}">PAGO</span> </td>
                                @elseif($articles->status == 'partial' && $articles->data_from == '')
                                    @php $total_to_pay = $total_to_pay + $articles->base_value @endphp
                                    <td><span class="bg-warning p-1" id="status_{{ $articles->article_month }}">PARCIAL
                                        </span></td>
                                @elseif($articles->status == 'pending' && $articles->extra_fees_value != 0 && $articles->data_from == '')
                                    @php $total_to_pay = $total_to_pay + $articles->base_value @endphp
                                    <td><span class="bg-warning p-1" id="status_{{ $articles->article_month }}">PARCIAL
                                        </span></td>
                                @elseif($articles->status == 'pending' && $articles->extra_fees_value != 0 && $articles->data_from != '')
                                    @php $total_to_pay = $total_to_pay + $articles->base_value @endphp
                                    <td><span class="bg-info p-1" id="status_{{ $articles->article_month }}">ESPERA
                                        </span></td>
                                @elseif($articles->status != 'pending' && $articles->extra_fees_value != 0 && $articles->data_from != '')
                                    @php $total_to_pay = $total_to_pay + $articles->base_value @endphp
                                    <td><span class="bg-info p-1" id="status_{{ $articles->article_month }}">ESPERA
                                        </span></td>
                                @elseif($articles->status == 'partial' && $articles->data_from != '')
                                    <td><span class="bg-warning p-1" id="status_{{ $articles->article_month }}">PARCIAL
                                        </span></td>
                                @elseif($articles->status == 'pending')
                                    @php $total_to_pay = $total_to_pay + $articles->base_value @endphp
                                    <td> <span class="bg-info p-1" id="status_{{ $articles->article_month }}">ESPERA
                                        </span> </td>
                                @else
                                    <td><span class="bg-danger p-1 text-white"
                                            id="status_{{ $articles->article_month }}"> ERRO </span></td>
                                @endif

                                {{-- {{$now->format('y')}}-{{ $receipt->code }} {{$articles->created_at_arti}} -  --}}
                                <td>

                                    @if ($articles->code != null && $articles->data_from == '' && $articles->status != 'pending')
                                        <?php
                                        $expl = explode('-', $articles->created_at_arti);
                                        
                                        $explode = explode('0', $expl[0]);
                                        ?>
                                        @if ($explode[1] == 2)
                                            {{ $explode[1] }}0 - {{ $articles->code }}
                                        @else
                                            {{ $explode[1] }} - {{ $articles->code }}
                                        @endif
                                    @else
                                        @if ($articles->code != null && $articles->status == 'pending' && $articles->extra_fees_value != 0)
                                            @if ($explode[1] == 2)
                                                {{ $explode[1] }}0 - {{ $articles->code }}
                                            @else
                                                {{ $explode[1] }} - {{ $articles->code }}
                                            @endif
                                        @else
                                            - -
                                        @endif
                                    @endif
                                </td>
                                @php $transaction_id=$articles->transaction_id; @endphp
                                @if ($articles->data_from != '' && $articles->status == 'partial')
                                    <td class="accoes-tesoraria">

                                        <a href="{{ route('requests.show', $articles->article_req_id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="far fa-eye"></i>
                                        </a>
                                        @if (
                                            !auth()->user()->hasAnyPermission('view-tesouraria-estudante') &&
                                                !auth()->user()->hasAnyPermission('secretario_view_RH'))
                                            @if (auth()->user()->hasAnyPermission('apagar_emolumentos'))
                                                <button class="btn btn-sm btn-danger" type="button"
                                                    onclick="deleteArticleRequest({{ $articles->article_req_id }})">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                            <button hidden value="checado{{ $i }}"
                                                style="  background-color: #38c172;color: #ffffff;" type="submit"
                                                id="checado{{ $i }}" class="btn  btn-sm checado">
                                                <i class="fa fa-file-invoice-dollar"></i>
                                            </button>
                                        @endif
                                        @if (auth()->user()->hasRole('superadmin'))
                                            <button style="background: #388cf1" hidden
                                                value="checado{{ $i }}"
                                                id="btn-referenciaEmolumento{{ $i }}" data-toggle="modal"
                                                data-target="#modal-referenciaMulticaixa" type="button"
                                                class="btn btn-sm text-white btn-referenciaEmolumento">
                                                <i style="font-size: 0.6pc" class="fas fa-r" aria-hidden="true"></i> <i
                                                    style="font-size: 0.6pc" class="fas fa-m" aria-hidden="true"></i>
                                            </button>
                                        @endif

                                    </td>
                                @elseif($articles->status == 'pending' && $articles->extra_fees_value != 0 && $articles->data_from != '')
                                    <td class="accoes-tesoraria">
                                        @if (auth()->user()->hasRole('superadmin'))
                                            <a href="{{ route('requests.show', $articles->article_req_id) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="far fa-eye"></i>
                                            </a>
                                        @endif
                                        @if (
                                            !auth()->user()->hasAnyPermission('view-tesouraria-estudante') &&
                                                !auth()->user()->hasAnyPermission('secretario_view_RH'))
                                            @if (auth()->user()->hasAnyPermission('apagar_emolumentos'))
                                                <button class="btn btn-sm btn-danger" type="button"
                                                    onclick="deleteArticleRequest({{ $articles->article_req_id }})">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif

                                            <button hidden value="checado{{ $i }}"
                                                style="  background-color: #38c172;color: #ffffff;" type="submit"
                                                id="checado{{ $i }}" class="btn  btn-sm checado">
                                                <i class="fa fa-file-invoice-dollar"></i>
                                            </button>
                                        @endif
                                        @if (auth()->user()->hasRole('superadmin'))
                                            <button style="background: #388cf1" hidden
                                                value="checado{{ $i }}"
                                                id="btn-referenciaEmolumento{{ $i }}" data-toggle="modal"
                                                data-target="#modal-referenciaMulticaixa" type="button"
                                                class="btn btn-sm text-white btn-referenciaEmolumento">
                                                <i style="font-size: 0.6pc" class="fas fa-r" aria-hidden="true"></i>
                                                <i style="font-size: 0.6pc" class="fas fa-m" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </td>
                                @elseif($articles->status == 'pending' && $articles->extra_fees_value != 0)
                                    @if (auth()->user()->hasRole('chefe_tesoureiro') ||
                                            auth()->user()->hasRole('tesoureiro') ||
                                            auth()->user()->hasRole('superadmin') ||
                                            auth()->user()->hasRole('promotor') ||
                                            auth()->user()->hasRole('presidente'))
                                        <td class="accoes-tesoraria">
                                            @if (auth()->user()->hasRole('superadmin'))
                                                <a href="{{ route('requests.show', $articles->article_req_id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                            @endif
                                            <a class="btn btn-info btn-sm"
                                                href="https://ispk.forlearn.ao/pt/payments/view-file/receipts/{{ $transaction_id }}"
                                                target="_blank">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            {{-- <a href="#" class="btn btn-sm btn-danger refund">
                                                                                    <i class="dynamic-datatable removebutton fas fa-undo"></i>
                                                                                </a> --}}
                                            @if (
                                                !auth()->user()->hasAnyPermission('view-tesouraria-estudante') &&
                                                    !auth()->user()->hasAnyPermission('secretario_view_RH'))
                                                @if (auth()->user()->hasAnyPermission('estorn-transacion'))
                                                    <button type="button"
                                                        onclick="showModal({{ $saldo }},{{ $transaction_id }},{{ $articles->article_req_id }})"
                                                        class="btn btn-sm btn-danger refund">
                                                        <i class="dynamic-datatable removebutton fas fa-undo"></i>
                                                    </button>
                                                @endif
                                                {{-- <a href="#" class="btn btn-warning btn-sm">
                                                                                        <i class="fas fa-envelope"></i>
                                                                                    </a> --}}
                                                <button hidden value="checado{{ $i }}"
                                                    style="  background-color: #38c172;color: #ffffff;" type="submit"
                                                    id="checado{{ $i }}" class="btn  btn-sm checado">
                                                    <i class="fa fa-file-invoice-dollar"></i>
                                                </button>
                                            @endif

                                            @if (auth()->user()->hasRole('superadmin'))
                                                <button style="background: #388cf1" hidden
                                                    value="checado{{ $i }}"
                                                    id="btn-referenciaEmolumento{{ $i }}"
                                                    data-toggle="modal" data-target="#modal-referenciaMulticaixa"
                                                    type="button"
                                                    class="btn btn-sm text-white btn-referenciaEmolumento">
                                                    <i style="font-size: 0.6pc" class="fas fa-r"
                                                        aria-hidden="true"></i> <i style="font-size: 0.6pc"
                                                        class="fas fa-m" aria-hidden="true"></i>
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                @elseif($articles->status == 'pending' && $articles->extra_fees_value == 0)
                                    <td class="accoes-tesoraria">
                                        @if (auth()->user()->hasRole('superadmin'))
                                            <a href="{{ route('requests.show', $articles->article_req_id) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="far fa-eye"></i>
                                            </a>
                                        @endif
                                        @if (
                                            !auth()->user()->hasAnyPermission('view-tesouraria-estudante') &&
                                                !auth()->user()->hasAnyPermission('secretario_view_RH'))
                                            @if (auth()->user()->hasAnyPermission('apagar_emolumentos'))
                                                <button class="btn btn-sm btn-danger" type="button"
                                                    onclick="deleteArticleRequest({{ $articles->article_req_id }})">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                            <button hidden value="checado{{ $i }}"
                                                style="  background-color: #38c172;color: #ffffff;" type="submit"
                                                id="checado{{ $i }}" class="btn  btn-sm checado">
                                                <i class="fa fa-file-invoice-dollar"></i>
                                            </button>
                                        @endif

                                        @if (auth()->user()->hasRole('superadmin'))
                                            <button style="background: #388cf1" hidden
                                                value="checado{{ $i }}"
                                                id="btn-referenciaEmolumento{{ $i }}" data-toggle="modal"
                                                data-target="#modal-referenciaMulticaixa" type="button"
                                                class="btn btn-sm text-white btn-referenciaEmolumento">
                                                <i style="font-size: 0.6pc" class="fas fa-r" aria-hidden="true"></i>
                                                <i style="font-size: 0.6pc" class="fas fa-m" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </td>
                                @elseif ($articles->status == 'total' || ($articles->status == 'partial' && $articles->data_from == ''))
                                    @if ($repet == 1)
                                        @php $repet=$articles->code; @endphp
                                        @if (auth()->user()->hasRole('chefe_tesoureiro') ||
                                                auth()->user()->hasRole('tesoureiro') ||
                                                auth()->user()->hasRole('superadmin') ||
                                                auth()->user()->hasRole('promotor') ||
                                                auth()->user()->hasRole('presidente'))
                                            <td class="accoes-tesoraria">
                                                @if (auth()->user()->hasRole('superadmin'))
                                                    <a href="{{ route('requests.show', $articles->article_req_id) }}"
                                                        class="btn btn-info btn-sm">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                @endif
                                                <a class="btn btn-info btn-sm"
                                                    href="https://ispk.forlearn.ao/pt/payments/view-file/receipts/{{ $transaction_id }}"
                                                    target="_blank">
                                                    <i class="fas fa-receipt"></i>
                                                </a>
                                                {{-- <a href="#" class="btn btn-sm btn-danger refund">
                                                                                            <i class="dynamic-datatable removebutton fas fa-undo"></i>
                                                                                        </a> --}}
                                                @if (
                                                    !auth()->user()->hasAnyPermission('view-tesouraria-estudante') &&
                                                        !auth()->user()->hasAnyPermission('secretario_view_RH'))
                                                    @if (auth()->user()->hasAnyPermission('estorn-transacion'))
                                                        <button type="button"
                                                            onclick="showModal({{ $saldo }},{{ $transaction_id }},{{ $articles->article_req_id }})"
                                                            class="btn btn-sm btn-danger refund">
                                                            <i class="dynamic-datatable removebutton fas fa-undo"></i>
                                                        </button>
                                                    @endif

                                                    {{-- <a href="#" class="btn btn-warning btn-sm">
                                                                                                <i class="fas fa-envelope"></i>
                                                                                            </a> --}}
                                                    <button hidden value="checado{{ $i }}"
                                                        style="  background-color: #38c172;color: #ffffff;"
                                                        type="submit" id="checado{{ $i }}"
                                                        class="btn  btn-sm checado">
                                                        <i class="fa fa-file-invoice-dollar"></i>
                                                    </button>
                                                @endif

                                                @if (auth()->user()->hasRole('superadmin'))
                                                    <button style="background: #388cf1" hidden
                                                        value="checado{{ $i }}"
                                                        id="btn-referenciaEmolumento{{ $i }}"
                                                        data-toggle="modal" data-target="#modal-referenciaMulticaixa"
                                                        type="button"
                                                        class="btn btn-sm text-white btn-referenciaEmolumento">
                                                        <i style="font-size: 0.6pc" class="fas fa-r"
                                                            aria-hidden="true"></i> <i style="font-size: 0.6pc"
                                                            class="fas fa-m" aria-hidden="true"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        @endif
                                    @elseif($repet != $articles->code && $articles->data_from == '')
                                        @php $repet=$articles->code; @endphp
                                        <td class="accoes-tesoraria">
                                            @if (auth()->user()->hasRole('superadmin'))
                                                <a href="{{ route('requests.show', $articles->article_req_id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                            @endif
                                            <a class="btn btn-info btn-sm"
                                                href="https://ispk.forlearn.ao/pt/payments/view-file/receipts/{{ $transaction_id }}"
                                                target="_blank">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            {{-- <a href="#" class="btn btn-sm btn-danger refund">
                                                                                        <i class="dynamic-datatable removebutton fas fa-undo"></i>
                                                                                    </a> --}}
                                            @if (
                                                !auth()->user()->hasAnyPermission('view-tesouraria-estudante') &&
                                                    !auth()->user()->hasAnyPermission('secretario_view_RH'))
                                                @if (auth()->user()->hasAnyPermission('estorn-transacion'))
                                                    <button type="button"
                                                        onclick="showModal({{ $saldo }},{{ $transaction_id }},{{ $articles->article_req_id }})"
                                                        class="btn btn-sm btn-danger refund">
                                                        <i class="dynamic-datatable removebutton fas fa-undo"></i>
                                                    </button>
                                                @endif
                                                {{-- <a href="#" class="btn btn-warning btn-sm">
                                                                                            <i class="fas fa-envelope"></i>
                                                                                        </a> --}}
                                                <button hidden value="checado{{ $i }}"
                                                    style="  background-color: #38c172;color: #ffffff;" type="submit"
                                                    id="checado{{ $i }}" class="btn  btn-sm checado">
                                                    <i class="fa fa-file-invoice-dollar"></i>
                                                </button>
                                            @endif

                                            @if (auth()->user()->hasRole('superadmin'))
                                                <button style="background: #388cf1" hidden
                                                    value="checado{{ $i }}"
                                                    id="btn-referenciaEmolumento{{ $i }}"
                                                    data-toggle="modal" data-target="#modal-referenciaMulticaixa"
                                                    type="button"
                                                    class="btn btn-sm text-white btn-referenciaEmolumento">
                                                    <i style="font-size: 0.6pc" class="fas fa-r"
                                                        aria-hidden="true"></i> <i style="font-size: 0.6pc"
                                                        class="fas fa-m" aria-hidden="true"></i>
                                                </button>
                                            @endif
                                        </td>
                                    @else
                                        @if (auth()->user()->hasRole('chefe_tesoureiro') ||
                                                auth()->user()->hasRole('tesoureiro') ||
                                                auth()->user()->hasRole('superadmin') ||
                                                auth()->user()->hasRole('promotor') ||
                                                auth()->user()->hasRole('presidente'))
                                            <td class="accoes-tesoraria">
                                                @if (auth()->user()->hasRole('superadmin'))
                                                    <a href="{{ route('requests.show', $articles->article_req_id) }}"
                                                        class="btn btn-info btn-sm">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                @endif
                                                @if (auth()->user()->hasRole('chefe_tesoureiro') ||
                                                        auth()->user()->hasRole('tesoureiro') ||
                                                        auth()->user()->hasRole('superadmin') ||
                                                        auth()->user()->hasRole('promotor') ||
                                                        auth()->user()->hasRole('presidente'))
                                                    <button hidden value="checado{{ $i }}"
                                                        style="  background-color: #38c172;color: #ffffff;"
                                                        type="submit" id="checado{{ $i }}"
                                                        class="btn  btn-sm checado">
                                                        <i class="fa fa-file-invoice-dollar"></i>
                                                    </button>
                                                @endif

                                                @if (auth()->user()->hasRole('superadmin'))
                                                    <button style="background: #388cf1" hidden
                                                        value="checado{{ $i }}"
                                                        id="btn-referenciaEmolumento{{ $i }}"
                                                        data-toggle="modal" data-target="#modal-referenciaMulticaixa"
                                                        type="button"
                                                        class="btn btn-sm text-white btn-referenciaEmolumento">
                                                        <i style="font-size: 0.6pc" class="fas fa-r"
                                                            aria-hidden="true"></i> <i style="font-size: 0.6pc"
                                                            class="fas fa-m" aria-hidden="true"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        @endif
                                    @endif
                                @elseif($articles->status == 'pending' && $articles->extra_fees_value == 0)
                                    @foreach ($disciplines as $discipline)
                                        @if ($discipline->article_req_id == $articles->article_req_id)
                                            @if ($discipline->discipline_id != null)
                                                @if (auth()->user()->hasRole('chefe_tesoureiro') ||
                                                        auth()->user()->hasRole('tesoureiro') ||
                                                        auth()->user()->hasRole('superadmin') ||
                                                        auth()->user()->hasRole('promotor') ||
                                                        auth()->user()->hasRole('presidente'))
                                                    <td class="accoes-tesoraria">
                                                        @if (auth()->user()->hasRole('superadmin'))
                                                            <a href="{{ route('requests.show', $articles->article_req_id) }}"
                                                                class="btn btn-info btn-sm">
                                                                <i class="far fa-eye"></i>
                                                            </a>
                                                        @endif
                                                        @if (
                                                            !auth()->user()->hasAnyPermission('view-tesouraria-estudante') &&
                                                                !auth()->user()->hasAnyPermission('secretario_view_RH'))
                                                            <a href="{{ route('requests.edit', $articles->article_req_id) }}"
                                                                class="btn btn-warning btn-sm">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @if (auth()->user()->hasAnyPermission('apagar_emolumentos'))
                                                                <button class="btn btn-sm btn-danger" type="button"
                                                                    onclick="deleteArticleRequest({{ $articles->article_req_id }})">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endif
                                                        @endif

                                                    </td>
                                                @endif
                                            @else
                                                @if (auth()->user()->hasRole('chefe_tesoureiro') ||
                                                        auth()->user()->hasRole('tesoureiro') ||
                                                        auth()->user()->hasRole('superadmin') ||
                                                        auth()->user()->hasRole('promotor') ||
                                                        auth()->user()->hasRole('presidente'))
                                                    <td class="accoes-tesoraria">
                                                        @if (auth()->user()->hasRole('superadmin'))
                                                            <a href="{{ route('requests.show', $articles->article_req_id) }}"
                                                                class="btn btn-info btn-sm">
                                                                <i class="far fa-eye"></i>
                                                            </a>
                                                        @endif
                                                        @if (
                                                            !auth()->user()->hasAnyPermission('view-tesouraria-estudante') &&
                                                                !auth()->user()->hasAnyPermission('secretario_view_RH'))
                                                            @if (auth()->user()->hasAnyPermission('apagar_emolumentos'))
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    type="button"
                                                                    onclick="deleteArticleRequest({{ $articles->article_req_id }})">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endif
                                                        @endif

                                                    </td>
                                                @endif
                                            @endif
                                        @endif
                                    @endforeach
                                @endif
                            </tr>
                            {{-- @endforeach --}}
                        @endif
                    @endif
                @endforeach
                {{-- @foreach ($deletedArticlesRequested as $articleDeleted)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td> </td>
                                                    @if ($articleDeleted->month != null)
                                                        @switch($articleDeleted->month)
                                                            @case(1)
                                                                <td>{{ $articleDeleted->display_name }} ( Janeiro {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @case(2)
                                                                <td>{{ $articleDeleted->display_name }} ( Fevereiro {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @case(3)
                                                                <td>{{ $articleDeleted->display_name }} ( Março {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @case(4)
                                                                <td>{{ $articleDeleted->display_name }} ( Abril {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @case(5)
                                                                <td>{{ $articleDeleted->display_name }} ( Maio {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @case(6)
                                                                <td>{{ $articleDeleted->display_name }} ( Junho {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @case(7)
                                                                <td>{{ $articleDeleted->display_name }} ( Julho {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @case(8)
                                                                <td>{{ $articleDeleted->display_name }} ( Agosto {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @case(9)
                                                                <td>{{ $articleDeleted->display_name }} ( Setembro {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @case(10)
                                                                <td>{{ $articleDeleted->display_name }} ( Outubro {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @case(11)
                                                                <td>{{ $articleDeleted->display_name }} ( Novembro {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @case(12)
                                                                <td>{{ $articleDeleted->display_name }} ( Dezembro {{ $articleDeleted->year }})</td>
                                                                @break
                                                            @default

                                                        @endswitch
                                                    @else
                                                        <td> {{ $articleDeleted->display_name }}</td>
                                                    @endif
                                                    <td></td>
                                                    <td>{{ $articleDeleted->base_value }}</td>
                                                    <td>{{ $articleDeleted->extra_fees_value}}</td>
                                                    <td> <span class="bg-danger p-1 text-white">Emolumento eliminado</span></td>
                                                    <td>--</td>

                                        </tr>
                                    @endforeach --}}
            @else
                <tr>
                    <td>Nenhum registo</td>
                </tr>
            @endif
            <input id="qdtIndex" type="hidden" value="{{ $i }}">
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>Total a pagar: <?php echo number_format($total_to_pay ?: 0, 0, ',', ' '); ?> <small>Kz</small></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

        </tfoot>

    </table>
</div>
<script>
    function generateReceiptForTransaction(id) {
        console.log(id);
        var myNewTab = window.open('about:blank', '_blank');
        let route = '{{ route('transactions.receipt', 0) }}'.slice(0, -1) + id
        $.ajax({
            method: "GET",
            url: route
        }).done(function(url) {
            console.log(url);
            myNewTab.location.href = url;
        });
    }
</script>
