<title>Tesouraria | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Tesouraria')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Tesouraria</li>
@endsection
@section('styles-new')
    <style>
        .margin-sub {
            padding-top: -4rem !important;
            margin-top: -4rem !important;
        }

        .fotoUserFunc {
            width: 70%;
            margin: 0px;
            padding: 0px;
            shape-outside: circle();
            clip-path: circle();
            border-radius: 50%;
            background-color: #c4c4c4;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 40%;
            width: 150px;
            height: 150px;
            -webkit-filter: brightness(.9);
            filter: brightness(.9);
            border: 5px solid #fff;
        }

        .item {
            margin-bottom: -1px;
            margin-left: 1px;
            border-top-left-radius: 10pc;
        }

        .ativo {
            background-color: white;
            color: black;
            border-right: white 1px solid;
            border-top: white 1px solid;
        }

        .dropdown_menu {
            min-width: auto;
            padding: 0px;
            margin: 0px;
            font-size: .9rem;
            color: #212529;
            text-align: left;
            list-style: none;
        }

        #container_menu {
            background-color: white;
            box-shadow: rgb(205 199 199) 2px 10px 8px 0px;
            padding: 0px;
            width: 70%;
            margin-left: 17px;
            z-index: 1000;
        }

        #subMenuLista {
            position: absolute;
            background-color: white;
            box-shadow: rgb(205 199 199) 2px 10px 8px 0px;
            padding: 0px;
            width: auto;
            margin-left: 43.5pc;
            margin-top: 3.3pc;
            z-index: 1000;
            height: auto;
        }

        .list-group-item:hover,
        #container_menu li:hover {
            background: #efefef;
        }

        .list-group-item a:hover {
            text-decoration: none;
        }
    </style>
@endsection
@section('models')

    <div class="modal fade" id="change-aluno-load" tabindex="-1" role="dialog" style="z-index: 9999999"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
        </div>
    </div>
    {{-- Modal que confirma a conta corrente --}}
    <div style="z-index: 9999999" class="modal fade" id="modal-contaCorrente" tabindex="-1"
        aria-labelledby="exampleModalLabel" araia-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl rounded mt-5  ">
            <div class="modal-content rounded" style="background-color: #dee2e6;">
                <div class="modal-header">
                    <h1 style="font-size: 1.4pc;" class="modal-title pl-0" id="exampleModalLabel">Conta corrente</h1>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('transactions.pdf') }}" target="_blank">
                    @csrf
                    <div class="modal-body">
                        <p class="lead">Caro utilizador/a <strong
                                style="font-weight: bold">{{ auth()->user()->name }}</strong> pretende gerar a conta
                            corrente do ano lectivo <strong style="font-weight: bold"
                                class="ano-lectivo-estudante">()</strong> ?</p>
                        <hr class="my-4">
                        <input hidden type="text" name="id_userContaCorrente" id="id_userContaCorrente">
                        <input hidden type="text" name="htmlContaCorrente" id="htmlContaCorrente">
                        <input type="hidden" name="ano_lectivo_estudante" id="ano-lectivo-estudante">
                        <br>
                        <button style="border-radius: 6px; background: #20c7f9" type="submit" class="btn btn-lg text-white mt-2 gerar-conta-corrente">
                            <i class="fas fa-file-pdf" aria-hidden="true"></i> 
                            Gerar PDF 
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--  Modal referência multicaixa  --}}
    <div style="z-index: 9999999" class="modal fade" id="modal-referenciaMulticaixa" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl rounded mt-5  ">
            <div class="modal-content rounded" style="background-color: #dee2e6;">
                <div class="modal-header">
                    <h1 style="font-size: 1.4pc;" class="modal-title pl-0" id="exampleModalLabel">Referência multicaixa</h1>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('getProxypay.referrencia') }}">
                    @csrf
                    <div class="modal-body">
                        <p class="lead">Caro utilizador/a <strong
                                style="font-weight: bold">{{ auth()->user()->name }}</strong> pretende gerar referência
                            multicaixa ? <i class="fa fa-bank"></i></p>
                        <hr class="my-4">
                        <input type="hidden" name="referenciaemolument" id="referencia-emolument">
                        <br>
                        <button style="border-radius:6px; background: #20c7f9" type="submit"
                            class="btn btn-lg text-white mt-2"></i>Gerar referência</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal dos emolumentos estornados --}}
    <div class="modal fade veraqui" id="modalEstorno" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-xl modal-dialog-centered">
            <div class="modal-content" style="z-index: 99999;border-top-left-radius: 10px;border-top-right-radius: 10px ">
                <div style="background:#ff7100a1;width: 100%;border-top-left-radius: 15px;border-top-right-radius: 15px"
                    class="m-0 p-1"></div>
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-recycle"></i> Estorno(s) </h4> <button type="button"
                        class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="container-fluid row colmd-12 m-0 p-0">
                        <div class="m-0 p-2 bd-highlight col-md-8">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Emolumentos / Propina</th>
                                        <th scope="col">Criado por</th>
                                        <th scope="col">Data</th>
                                        <th scope="col">Factura/Recibo nº</th>
                                        <th scope="col">Acções</th>
                                    </tr>
                                </thead>
                                <tbody id="lista_estorno">
                                </tbody>
                            </table>
                        </div>
                        <div class="m-0 p-1 mt-4  bd-highlight col-md-4">
                            <div class="mb-3 " style="max-width: 540px;  ">
                                <div class="d-flex border rounded">
                                    <div class="p-4 m-0" style="background: #ff7100a1; ">
                                    </div>
                                    <div class="">
                                        <div class="">
                                            <h5 class="">QUADRO RESUMO</h5>
                                            <p class="m">De acordo a presente data o valor total das transações
                                                anuladas é: <b id="valorTransEstorno"></b>Kz</p>
                                            <p style="border-top: rgb(230, 222, 222) 0.5px solid"
                                                class="card-text m-0 p-0 ">
                                                <small id="intervaloEstorno" class="text-muted"></small> <i
                                                    class="fa fa-spinner fa-spin"></i>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    {{-- Modal de confirmação para apagar emolumento requerido --}}
    <div class="modal fade" id="delete_article">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('modal.confirm_title')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <p><span>@lang('modal.confirm_text')</span>&nbsp;<span class="modal-confirm-text"></span></p>
                </div>
                <div class="modal-footer">
                    <a href="" class="btn forlearn-btn" id="delete-btn">
                        <i class="far fa-check-square"></i>@lang('modal.confirm_button')
                    </a>
                    <button type="button" class="btn forlearn-btn" data-dismiss="modal">
                        <i class="far fa-window-close"></i>@lang('modal.cancel_button')
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('body')

    @if (auth()->user()->hasRole(['superadmin']))
        <form hidden class="col-6" method="POST" action="{{ route('update.credet') }}">
            @csrf
            <div class="form-group">
                <label for="exampleInputEmail1">Valor</label>
                <input type="text" class="form-control" name="valor" id="exampleInputEmail1"
                    aria-describedby="emailHelp" placeholder="">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Aluno</label>
                <input type="text" class="form-control" name="id_aluno" id="exampleInputPassword1" placeholder="">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    @endif

    <form action="{{ route('request_create') }}" method="post" id="data-check" class="margin-sub">
        @csrf
        <div class="content">
            <div class="container-fluid">
                <div class="row ">
                    <div class="col">
                        <div class="d-flex gap-1 justify-content-between align-items-center pr-0 pl-0">
                            <div class="flex-grow-1">
                                <div class="">
                                    <input name="selectAnoLetivo" type="hidden" id="selectAnoLetivo" value="0">
                                    <label>@lang('Payments::requests.student')</label>
                                    <select data-live-search="true" required
                                        class="selectpicker form-control form-control-sm" required="" id="user"
                                        data-actions-box="false" data-selected-text-format="values" name="user"
                                        tabindex="-98">
                                        @foreach ($users as $item)
                                            @if (isset($id_student))
                                                @if ($item->id == $id_student)
                                                    <option value="{{ $item->id }}" selected>{{ $item->display_name }}
                                                    </option>
                                                @else
                                                    <option value="{{ $item->id }}">{{ $item->display_name }}</option>
                                                @endif
                                            @else
                                                <option value="{{ $item->id }}">{{ $item->display_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex-grow-0 d-flex gap-1 justify-content-center foto-estudante pl-3 pr-3">
                                <div class="fotoUserFunc"></div>
                            </div>

                            <div class="flex-grow-0 d-flex gap-0 justify-content-end mt-4">

                                @if (auth()->user()->hasAnyPermission(['view-tesouraria-estudante']))
                                    <div id="id_matricula_student">

                                    </div>
                                @else
                                    <div class="col-auto">
                                        <div class="form-group">
                                            @if (isset($id_matricula))
                                                <div class="form-group">
                                                    <a data-toggle="tooltip" data-placement="bottom" title="Matricula "
                                                        id="id_matricula"
                                                        class="p-2 pr-3 pl-3 btn btn-sm mt-3 mb-3 element btn-dark"
                                                        href="{{ route('matriculations.show', $id_matricula) }}">
                                                        <i class="fa fa-user"></i>
                                                        <i class="fa-solid fa-m"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="form-group m-0 div-matricula">
                                                    <a data-toggle="tooltip" data-placement="bottom" title="Matricula "
                                                        target="_blank" id="id_matricula_student"
                                                        class="p-2 pr-3 pl-3 btn btn-sm mt-3 mb-3 element btn-dark"
                                                        href="">
                                                        <i class="fa fa-user"></i>
                                                        <i class="fa-solid fa-m"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="col-auto">
                                    <div class="form-group">
                                        @if (auth()->user()->hasAnyPermission(['view-tesouraria-estudante']))
                                            <input id="valorid_studentid_student" type="hidden" value="0">
                                            <a data-toggle="tooltip" data-placement="bottom" target="_blank"
                                                title="Perfil estudante"
                                                style="background-color: black; color:white;display:none;"
                                                class="p-2 pr-3 pl-3 btn btn-sm mt-3 mb-3 id_student element">
                                                <i class="fa fa-user"></i>
                                                <i class="fa-solid fa-P"></i>
                                            </a>
                                        @else
                                            @if (isset($id_student))
                                                <input id="valorid_studentid_student" type="hidden"
                                                    value="{{ $id_student }}">
                                                <a data-toggle="tooltip" data-placement="bottom" target="_blank"
                                                    title="Perfil estudante"
                                                    href="{{ route('users.show', $id_student) }}"
                                                    style="background-color: black; color:white"
                                                    class="p-2 pr-3 pl-3 btn btn-sm mt-3 mb-3 element" id="bnt-new">
                                                    <i class="fa fa-user"></i>
                                                    <i class="fa-solid fa-P"></i>
                                                </a>
                                            @else
                                                <input id="valorid_studentid_student" type="hidden" value="0">
                                                <a data-toggle="tooltip" data-placement="bottom" target="_blank"
                                                    title="Perfil estudante" style="background-color: black; color:white"
                                                    class="p-2 pr-3 pl-3 btn btn-sm mt-3 mb-3 id_student element">
                                                    <i class="fa fa-user"></i>
                                                    <i class="fa-solid fa-P"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                @if (auth()->user()->hasAnyPermission(['view-tesouraria-estudante']))
                                    <div id="bnt-new">

                                    </div>
                                    <div hidden class="col-auto contaCorrte">
                                        <div class="form-group">
                                            <button data-toggle="modal" data-target="#modal-contaCorrente"
                                                target="_blank" type="button"
                                                style="background-color: #00d55a; color:white"
                                                class="p-2 pr-3 pl-3 btn btn-sm  mt-3 mb-3 element" id="call-pdf">
                                                <i class="fa fa-plus-square"></i>
                                                Conta corrente
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <!-- <div class="col-auto">
                                                                <div class="form-group div-requerimento" hidden>
                                                                    <a data-toggle="tooltip" data-placement="bottom" title="Requerimento"
                                                                        href="" style="background-color: #38c172; color:white"
                                                                        class="p-2 pr-3 pl-3 btn btn-sm mt-3 mb-3 element" id="bnt-new">
                                                                        <i class="fas fa-plus-square"></i>
                                                                        <i class="fa-solid fa-r"></i>
                                                                    </a>
                                                                </div>
                                                            </div> -->
                                    <div hidden class="col-auto contaCorrte">
                                        <div class="form-group">
                                            <button data-toggle="modal" data-target="#modal-contaCorrente"
                                                data-toggle="tooltip" data-placement="bottom" title="Conta corrente"
                                                target="_blank" type="button"
                                                style="background-color: #00d55a; color:white"
                                                class="p-2 pr-3 pl-3 btn btn-sm  mt-3 mb-3 element" id="call-pdf">
                                                <i class="fa fa-plus-square"></i>
                                                <i class="fa fa-c"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                <div hidden class="col-auto observacao">
                                    <div class="form-group">
                                        <a data-toggle="tooltip" data-placement="bottom" title="Observações"
                                            href="" style="background-color: #ffa500; color:white"
                                            id="call-observation" target="_blank"
                                            class="p-2 pr-3 pl-3 btn btn-sm  mt-3 mb-3 element">
                                            <i class="fas fa-plus-square"></i>
                                            <i class="fas fa-o"></i>
                                            <span class="badge badge-pill bg-danger rounded-circle" id="observation"
                                                style="font-size: 12pt;">0</span>
                                        </a>
                                    </div>
                                </div>
                                @if (auth()->user()->hasAnyPermission(['estorn-transacion']))
                                    <div hidden class="col-auto observacao">
                                        <div class="form-group">
                                            <button data-toggle="tooltip" data-placement="bottom" type="button"
                                                title="Estorno(s)" style="background-color: #ff7100ab; color:white"
                                                data-bs-toggle="modal" data-bs-target="#modalEstorno" id="callEstorno"
                                                class="p-2 pr-3 pl-3 btn btn-sm  mt-3 mb-3 element">
                                                <span class="badge" id="observation" style="color: black"><i
                                                        class="fas fa-receipt">
                                                        <p
                                                            style="font-size: 1.1pc; color:#ffa500 ; position: relative; z-index: 999;margin-top: -17px">
                                                            X</p>
                                                    </i></span>
                                                <i class="fas fa-e"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                @if (auth()->user()->hasAnyPermission(['estorn-transacion']))
                                    <div class="col-auto observacao">
                                        <div class="form-group">
                                            <a data-toggle="tooltip" data-placement="bottom" title="Extracto"
                                                href="" style="background-color: #979592ab; color:white"
                                                id="call-extract" target="_blank"
                                                class="p-2 pr-3 pl-3 btn btn-sm  mt-3 mb-3 element">
                                                <span class="badge" id="extract" style="color: black"><i
                                                        class="fas fa-file-text">
                                                        <p
                                                            style="font-size: 1.1pc; color:#979592ab ; position: relative; z-index: 999;margin-top: -17px">
                                                            X</p>
                                                    </i></span>
                                                Ex
                                            </a>
                                        </div>
                                    </div>
                                @endif

                            </div>

                        </div>
                    </div>

                    <div id="bolseiro" class="margin-sub"
                        style="float:right;margin-right:20px;transform:translateY(17px);font-size:18px;text-transform: uppercase;">
                        <p></p>
                    </div>

                    <div class="container-fluid mt-1 ml-0 pl-1 row">
                        <div class="col-md-9 mr-0 pr-0">
                            <ul id="listaBotunAnoLectivo" class="nav nav-tabs"></ul>
                        </div>
                        <div style="padding: 3px; background:#e6f8ff;margin: 0px;"
                            class="border-left col-md-3 text-center" id="dadosSaldoCarteira" hidden>
                            <h6 class="mb-1 pt-1">
                                <small style="font-size: 1pc" class="text-muted">SALDO EM
                                    CARTEIRA:
                                </small>
                                <b style="font-size: 1.2pc" id="saldo_carteira"></b>
                                <small>Kz</small>
                                <a href="@isset($id_student) {{ $id_student }} @endisset "
                                    id="historicos_saldo_carteira" target="_blank"><i class="fa fa-wallet ml-3"></i>
                                </a>
                            </h6>
                        </div>
                    </div>

                    <div onmouseout="close_filtroEmolemento()" onmouseover="open_emolumento()" id="container_menu"
                        class="container " hidden>
                        <ul class="dropdown_menu p-2"></ul>
                    </div>
                    <div class="card">
                        <div class="card-body" id="content-table">
                            <div id="group">
                                <table id="requests-table" class="table table-striped table-hover">
                                    <thead>
                                        <th>#</th>
                                        <th><i style="font-size: 15px;color: #c3c3c3;" class="fa fa-check-square"></i>
                                        </th>
                                        <th>Emolumento / Propina</th>
                                        <th>Valor</th>
                                        <th>Multa</th>
                                        <th>Pagamento</th>
                                        <th>Factura/Recibo nº</th>
                                        <th>Acções</th>
                                    </thead>
                                </table>
                            </div>
                            <div class="mb-2" id="container">

                            </div>
                            @if (!auth()->user()->hasAnyPermission(['view-tesouraria-estudante']) && !auth()->user()->hasAnyPermission('secretario_view_RH'))
                                <button hidden id="btnDeleteAllarticl" type="button" class="btn btn-sm btn-warning">
                                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{-- Modal para armazenar motivo do estorno --}}
    <form action="{{ route('estornar') }}" method="post">
        @csrf
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background: #00a7d0">
                        <h5 class="modal-title" id="exampleModalLabel">Efetuar estorno</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="group-control" hidden>
                            <input class="form-control" name="transaction_id" type="number" id="pass_id">
                            <input class="form-control" name="article_id" type="number" id="article_id">

                        </div>
                        <div class="group-control">
                            <label for="">Motivo do estorno:</label>
                            <textarea name="motivo_estorno" id="motivo_estorno" cols="20" rows="2" class="form-control" required>
                    </textarea>
                        </div>
                        <div class="custom-control custom-checkbox mt-2 saldo_estorno" hidden>
                            {{-- <input type="checkbox" class="custom-control-input" id="estorno_saldo" name="estorno_saldo"> --}}
                            <label class="custom-control-label " for="customCheck1">Neta transação foi utilizado um
                                saldo</label> <small style="font-size: 1pc" id="valor_saldoEstornado"></small> Kz
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts-new')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    @parent
    <script>
        var dataTableBaseUrl = '{{ route('requests.ajax', 0) }}'.slice(0, -1);
        var dataTablePayments = null;
        var transactionBtn = $('#transaction-btn');
        var selectUser = $('#user');
        var historico_sc = $('#historicos_saldo_carteira');
        var selectedUserId = null;
        var anolectivo = $("#anolectivo")
        var anolectivo_ativo = null;

        var monthOut_Dezembro = [];
        var monthJan_Julho = [];

        var getboolenOut_Dez = null;
        var getboolenJan_Julho = null;

        var getMonthOut_Dez = [];
        var getMonthsJan_Julho = [];

        var deleteAllArticl = [];
        var listaArticlDeleted = []
        var listaArticlReferencia = []

        var ano = null;
        var id_student = $(".id_student");
        var id_studentPerfil = $(".id_studentPerfil");
        var valorid_studentid_student = $("#valorid_studentid_student");
        var id_matricula = $("#id_matricula");
        var valorMatricula = valorid_studentid_student.val();
        var selectAnoLetivo = $("#selectAnoLetivo");
        var saldo_carteira = $("#saldo_carteira");
        var submenu = false;
        var valorTransEstorno = $("#valorTransEstorno");
        var intervaloEstorno = $("#intervaloEstorno");
        var htmlContaCorrente = null;
        var vetorUserContaCorrente = [];
        var vetorAnolectivoSem_matricula = [];
        var resultData = null;
        var referencia_emolument = $("#referencia-emolument");
        var ano_lectivo_activo = 0;

        getExtract();

        function getExtract() {
            $("#call-extract").attr('href', "https://ispk.forlearn.ao/pt/reports/extract/" + selectUser.val() + "/" +
                ano_lectivo_activo);

        }

        $(".gerar-conta-corrente").click(function(e) {
            $("#modal-contaCorrente").modal('hide')
        });

        $("#callEstorno").click(function(e) {
            $(".veraqui").modal('toggle');
        });

        function subMenulista() {
            $("#subMenuLista").attr('hidden', false)
            $("#submenu").attr('class', "")
            $("#submenu").attr('class', "fa fa-angle-down")
        }

        function subMenuListaHidden() {
            $("#subMenuLista").attr('hidden', true)
            $("#submenu").attr('class', "")
            $("#submenu").attr('class', "fa fa-chevron-right")
        }

        function subMenuTesourario() {

            $("#submenu").attr('class', "")
            $("#submenu").attr('class', "fa fa-angle-down")
            $("#subMenuLista").attr('hidden', false)

        }

        function subMenuTesourarioHidden(params) {
            $("#submenu").attr('class', "")
            $("#submenu").attr('class', "fa fa-chevron-right")
            $("#subMenuLista").attr('hidden', true)

        }

        function showModal(valor_credit, transaction_id, article_id) {

            valor_credit != 0 ? $(".saldo_estorno").attr('hidden', false) : $(".saldo_estorno").attr('hidden', true)
            var saldo = Number.parseFloat(valor_credit)
            Number.parseFloat(saldo)
            $("#valor_saldoEstornado").text(saldo.toLocaleString('pt-br', {
                minimumFractionDigits: 2
            }))
            // $("#estorno_saldo").val(saldo);
            $('#exampleModal').modal('toggle');
            $('#pass_id').val("");
            $('#article_id').val("");
            $('#pass_id').val(transaction_id);
            $("#article_id").val(article_id);
        }

        function deleteArticleRequest(articleRequestId) {
            var url = '{{ route('delete_article_request', ':slug') }}';
            url = url.replace(':slug', articleRequestId);
            //window.location.href=url;
            $("#delete-btn").attr('href', url);

            $("#article_request_id").val("");
            $("#article_request_id").val(articleRequestId)
            $("#delete_article").modal('toggle');
        }

        $("#btnDeleteAllarticl").click(function(e) {
            deleteArticleRequest(listaArticlDeleted)
        });

        $(function() {

            $(".element").hover(function() {
                $(this).tooltip('show');
            });


            $("#motivo_estorno").val("");
            $("#container").empty();
            $("#bnt-new").attr('hidden', true)

            $("#user").change(function() {
                var userId = $("#user").val();
                $("#container").empty();
                countObservationsBy(userId);
                listaAno_lectivo(userId)
                $("#bnt-new").attr('hidden', true)
                $('#group').show()
                getExtract();
                var jabutiImg = null;
                jabutiImg = new Image();
                $(".fotoUserFunc").css('background-image', "")
            });


            @if (auth()->user()->can('manage-requests-others'))
                if (!$.isEmptyObject(selectUser)) {


                    if (valorid_studentid_student.val() == 0) {
                        selectedUserId = Utils.setSelectedUserOnLoad('selectedUserPayments', selectUser[0]);
                        selectUser.selectpicker('val', selectedUserId);
                        Utils.updatedSelectedUserInSession('selectedUserPayments', selectedUserId);
                        listaAno_lectivo(selectedUserId)
                        getFiltroEmolumento_student(selectedUserId);
                        // getinforma


                    } else {
                        selectedUserId = valorid_studentid_student.val();
                        listaAno_lectivo(selectedUserId)
                        getFiltroEmolumento_student(selectedUserId);
                        // getinforma

                    }
                    let routePerfil_user = ("{{ route('users.show', 'id_user') }}").replace('id_user',
                        selectedUserId);
                    id_student.attr('href', routePerfil_user)

                    historico_sc.attr('href', "https://ispk.forlearn.ao/pt/payments/historico_saldo/" +
                        selectUser.val());

                    selectUser.change(function() {
                        get_bolseiro(selectUser.val());
                        getExtract();
                        historico_sc.attr('href',
                            "https://ispk.forlearn.ao/pt/payments/historico_saldo/" + $(this).val());


                        var jabutiImg = null;
                        jabutiImg = new Image();
                        $(".fotoUserFunc").css('background-image', "")

                        transactionBtn.prop('hidden', true);
                        selectedUserId = parseInt(this.value);
                        $("#id_matricula_student").attr('hidden', true)


                        let routePerfil_user = ("{{ route('users.show', 'id_user') }}").replace('id_user',
                            selectedUserId);
                        id_student.attr('href', routePerfil_user)
                        id_studentPerfil.attr('href', '')
                        id_studentPerfil.attr('href', routePerfil_user)


                        if (valorMatricula == 0) {
                            $("#id_matricula_student").attr('hidden', false)
                            var element_id_matricula_student = document.getElementById(
                                "id_matricula_student");
                            element_id_matricula_student.href = '/users/matriculations_user/' +
                                selectedUserId + '/' + anolectivo.val();
                        } else {
                            $("#id_matricula_student").attr('hidden', true)
                            valorMatricula = valorid_studentid_student.val();
                            var element = document.getElementById("id_matricula");
                            element.href = '/users/matriculations_user/' + selectedUserId + '/' + anolectivo
                                .val();
                        }
                        listaAno_lectivo(selectedUserId)
                        getFiltroEmolumento_student(selectedUserId)
                        $("#bnt-new").attr('hidden', true)
                        Utils.updatedSelectedUserInSession('selectedUserPayments', selectedUserId);
                        $('#group').show()
                    });
                }
            @else
                if (selectedUserId == null) {
                    selectedUserId = $("#user").val()
                    listaAno_lectivo(selectedUserId)
                    getFiltroEmolumento_student(selectedUserId);
                    id_student.attr('hidden', true)
                    $(".div-matricula").attr('hidden', true)
                    $("#call-observation").attr('hidden', true)
                    // $(".div-requerimento").attr('hidden',true)
                }
            @endif
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');



        function countObservationsBy(userId) {
            $.ajax({
                url: "/payments/count_Observations_by/" + userId,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(response) {
                    if (response > 0) {
                        $("#observation").removeClass('badge-light');
                        $("#observation").addClass('badge-danger');
                    } else {
                        $("#observation").removeClass('badge-danger');
                        $("#observation").addClass('badge-light');
                    }

                    $("#observation").text(response);
                }
            });
        }
        // Metodo que busca os item da batela, com o seu repetico ano lectivo 
        // [$item->id_usuario, 'matri'=> $item->id]
        function listItens(anoLectivo) {
            var userId = selectedUserId
            selectAnoLetivo.val(anoLectivo);
            $(".textContaCorrente").attr('hidden', false)
            $(".div-matricula").attr('hidden', false)
            //  $(".div-requerimento").attr('hidden',false)
            $("#btnDeleteAllarticl").attr('hidden', true)

            if (valorMatricula == 0) {
                $("#id_matricula_student").attr('hidden', false)
                var element_id_matricula_student = document.getElementById("id_matricula_student");
                element_id_matricula_student.href = '/users/matriculations_user/' + userId + '/' + anoLectivo;
            } else {
                if (valorMatricula == userId) {
                    valorMatricula = valorid_studentid_student.val();
                    var element = document.getElementById("id_matricula");
                    element.href = '/users/matriculations_user/' + valorMatricula + '/' + anoLectivo;
                    id_matricula.prop('hidden', false);
                } else {

                    valorMatricula = valorid_studentid_student.val();
                    var element = document.getElementById("id_matricula");
                    element.href = '/users/matriculations_user/' + userId + '/' + anoLectivo;
                    id_matricula.prop('hidden', false);

                }

            }
            if (anolectivo_ativo == null) {
                anolectivo_ativo = anoLectivo
                vetorUserContaCorrente = []

            } else {
                $("#anolectivo" + anolectivo_ativo).attr('class', '')
                $("#anolectivo" + anolectivo_ativo).attr('class', 'nav-link btn-outline-dark')
                anolectivo_ativo = anoLectivo
                vetorUserContaCorrente = []
                $("#change-aluno-load").modal('show');
            }
            $("#anolectivo" + anoLectivo).attr('class', '')
            $("#anolectivo" + anoLectivo).attr('class', 'nav-link btn-outline-dark ativo')

            $("#outroEmolumento").attr('class', '')
            $("#outroEmolumento").attr('class', 'nav-link btn-outline-dark')

            $("#studentFinalista").attr('class', '')
            $("#studentFinalista").attr('class', 'nav-link btn-outline-dark')

            var dados = anolectivo_ativo + "," + userId;
            // let routeToCreate = ("{{ route('user_requests_create', 'id_user') }}").replace('id_user', dados);
            // document.getElementById('bnt-new').setAttribute('href', routeToCreate);
            // $("#bnt-new").attr('hidden',false)


            $.ajax({
                url: "/payments/request_transaction_by/" + userId + "/" + anoLectivo,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(response) {
                    //$('#exampleModalCenter').modal('hide');
                }
            }).done(function(data) {



                var valortotalTran = null;
                var result = null;
                $("#change-aluno-load").modal('hide');
                $('.modal-backdrop').attr('hidden', true);


                if (data['data'] == false) {
                    $('#container').empty()
                    $('#group').show()
                    var mensagem = "<h5>Nenhum registo</h5>"
                    $("#container").append(mensagem);
                } else {

                    getConsultaPropina_apagar(selectedUserId);
                    $('#container').empty()
                    $('#group').hide(); //Esconder a tabela principal antes de chamar a dos resultados

                    $('#container').html(data
                        .html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)
                    htmlContaCorrente = data.html;
                }

                if (data['data'] == false) {
                    $('#lista_estorno').empty()
                    $('#intervaloEstorno').empty()
                    $('#valorTransEstorno').empty()
                } else {
                    $('#lista_estorno').empty()
                    $('#lista_estorno').html(data
                        .data_html
                    ); //chamar outra view dentro da mesma view (substituindo a tabela princiapl) sobre sobre os estorno
                    $.each(data.detalheEstorno['totalValorTrans'], function(indexInArray, valor) {
                        valortotalTran += valor
                    });
                    result = valortotalTran == null ? 0 : valortotalTran
                    $('#intervaloEstorno').text(data.detalheEstorno['data_anolectivo'])
                    Number.parseFloat(result)
                    valorTransEstorno.text(result.toLocaleString('pt-br', {
                        minimumFractionDigits: 2
                    }))

                }
                verMonth_serPagos();
                verDeleteAllArticl();
                if (vetorUserContaCorrente.length < 2) {
                    $("#htmlContaCorrente").val(htmlContaCorrente)
                    $("#id_userContaCorrente").val(userId)
                    vetorUserContaCorrente.push(htmlContaCorrente)
                    vetorUserContaCorrente.push(userId)
                    $("#ano-lectivo-estudante").val(anolectivo_ativo)

                    getStudentInfo(userId)
                    $.each(resultData, function(index, item) {
                        if (item.id == anolectivo_ativo) {
                            $(".ano-lectivo-estudante").text("")
                            $(".ano-lectivo-estudante").text(item.current_translation.display_name)
                        }
                    })

                }
                $("#change-aluno-load").modal('hide')
            })
            $("#change-aluno-load").modal('hide')

            setTimeout(() => {
                $("#change-aluno-load").modal('hide')
            }, 3000);

        }

        // metodo que chama doutros emolumentos requerido.
        function getOutrosEmolumentoRequerido() {
            var userId = selectedUserId;
            selectAnoLetivo.val(0);
            $("#id_userContaCorrente").val(userId)
            $("#ano-lectivo-estudante").val(null)
            $("#btnDeleteAllarticl").attr('hidden', true)
            $("#change-aluno-load").modal('show')


            $(".ano-lectivo-estudante").text("")
            $(".textContaCorrente").attr('hidden', true)

            $("#outroEmolumento").attr('class', '')
            $("#outroEmolumento").attr('class', 'nav-link btn-outline-dark ativo')
            $("#anolectivo" + anolectivo_ativo).attr('class', '')
            $("#anolectivo" + anolectivo_ativo).attr('class', 'nav-link btn-outline-dark')
            $.ajax({
                url: "/payments/getOutrosEmolumentoRequerido/" + userId + "/" + vetorAnolectivoSem_matricula,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(response) {

                }
            }).done(function(data) {

                $("#change-aluno-load").modal('hide');
                $('.modal-backdrop').attr('hidden', true);

                var valortotalTran = null;
                $(".div-matricula").attr('hidden', true)
                //   $(".div-requerimento").attr('hidden',true)

                var result = null;
                if (data['data'] == false) {
                    $('#container').empty()
                    $('#group').show()
                    var mensagem = "<h5>Nenhum registo</h5>"
                    $("#container").append(mensagem);
                } else {
                    getConsultaPropina_apagar(selectedUserId);
                    $('#container').empty()
                    $('#group').hide(); //Esconder a tabela principal antes de chamar a dos resultados

                    $('#container').html(data
                        .html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)
                    htmlContaCorrente = data.html;
                }

                if (data['data'] == false) {
                    $('#lista_estorno').empty()
                    $('#intervaloEstorno').empty()
                    $('#valorTransEstorno').empty()
                } else {
                    $('#lista_estorno').empty()
                    $('#lista_estorno').html(data
                        .data_html
                    ); //chamar outra view dentro da mesma view (substituindo a tabela princiapl) sobre sobre os estorno
                    $.each(data.detalheEstorno['totalValorTrans'], function(indexInArray, valor) {
                        valortotalTran += valor
                    });
                    result = valortotalTran == null ? 0 : valortotalTran
                    $('#intervaloEstorno').text(data.detalheEstorno['data_anolectivo'])
                    Number.parseFloat(result)
                    valorTransEstorno.text(result.toLocaleString('pt-br', {
                        minimumFractionDigits: 2
                    }))

                }

                verMonth_serPagos();
                verDeleteAllArticl();
                $("#htmlContaCorrente").val(htmlContaCorrente)
                getStudentInfo(userId)
            })
        }

        // Metodo que lista os emolumentos para o finalista aqui
        function getEmolumentoFinalista(lective_years_id) {
            var userId = selectedUserId;
            selectAnoLetivo.val(0);
            anolectivo_ativo = lective_years_id;
            $("#id_userContaCorrente").val(userId)
            $("#ano-lectivo-estudante").val(null)
            $("#btnDeleteAllarticl").attr('hidden', true)
            $("#change-aluno-load").modal('show')


            $(".ano-lectivo-estudante").text("")
            $(".textContaCorrente").attr('hidden', true)

            $("#studentFinalista").attr('class', '')
            $("#studentFinalista").attr('class', 'nav-link btn-outline-dark ativo')

            $("#outroEmolumento").attr('class', '')
            $("#outroEmolumento").attr('class', 'nav-link btn-outline-dark')
            $("#anolectivo" + anolectivo_ativo).attr('class', '')
            $("#anolectivo" + anolectivo_ativo).attr('class', 'nav-link btn-outline-dark')

            //   $(".div-requerimento").attr('hidden',false)
            var dados = lective_years_id + "," + userId;


            $.ajax({
                url: "/payments/getEmolumentoFinalista/" + userId + "/" + lective_years_id,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(response) {

                }
            }).done(function(data) {

                $("#change-aluno-load").modal('hide');
                $('.modal-backdrop').attr('hidden', true);
                var valortotalTran = null;
                $(".div-matricula").attr('hidden', true)

                var result = null;
                if (data['data'] == false) {
                    $('#container').empty()
                    $('#group').show()
                    var mensagem = "<h5>Nenhum registo</h5>"
                    $("#container").append(mensagem);
                } else {
                    getConsultaPropina_apagar(selectedUserId);
                    $('#container').empty()
                    $('#group').hide(); //Esconder a tabela principal antes de chamar a dos resultados

                    $('#container').html(data
                        .html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)
                    htmlContaCorrente = data.html;
                }

                if (data['data'] == false) {
                    $('#lista_estorno').empty()
                    $('#intervaloEstorno').empty()
                    $('#valorTransEstorno').empty()
                } else {
                    $('#lista_estorno').empty()
                    $('#lista_estorno').html(data
                        .data_html
                    ); //chamar outra view dentro da mesma view (substituindo a tabela princiapl) sobre sobre os estorno
                    $.each(data.detalheEstorno['totalValorTrans'], function(indexInArray, valor) {
                        valortotalTran += valor
                    });
                    result = valortotalTran == null ? 0 : valortotalTran
                    $('#intervaloEstorno').text(data.detalheEstorno['data_anolectivo'])
                    Number.parseFloat(result)
                    valorTransEstorno.text(result.toLocaleString('pt-br', {
                        minimumFractionDigits: 2
                    }))

                }

                verMonth_serPagos();
                verDeleteAllArticl();
                $("#htmlContaCorrente").val(htmlContaCorrente)
                getStudentInfo(userId)
            })
        }



        // Metodo que lista os botos do ano lectivo do aluno.
        function listaAno_lectivo(selectedUserId) {

            $("#change-aluno-load").modal('show');

            $.ajax({
                url: "/payments/getAnolectivo_student/" + selectedUserId,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {

                var load = false;
                var sem_matricula = false;
                vetorAnolectivoSem_matricula = [];
                var listaOutrosBtn = "";
                var listafinalista = "";
                resultData = data['data'].display_nameAnolectivo;
                var anolectivoFinalista = data['data'].getConfirmation_finalista;
                var resultDataAnolectivoSem_matricula = data['data'].anolectivoSem_matricula
                if (resultDataAnolectivoSem_matricula.length > 0) {
                    sem_matricula = true
                    $.each(resultDataAnolectivoSem_matricula, function(index, item) {
                        vetorAnolectivoSem_matricula.push(item.id)
                    });

                }
                if (resultData.length > 0) {
                    var listabottun = "";
                    $("#dadosSaldoCarteira").attr('hidden', false)
                    var saldo = Number.parseFloat(data['data'].getSaldoCarteira[0].credit_balance)
                    Number.parseFloat(saldo)
                    saldo_carteira.text(saldo.toLocaleString('pt-br', {
                        minimumFractionDigits: 2
                    }))
                    saldo_carteira.text()

                    $("#listaBotunAnoLectivo").empty();
                    $.each(resultData, function(key, item) {
                        if (data['anoativo'] == item.id) {
                            anolectivo_ativo = item.id;
                            listItens(item.id)
                            load = true;
                            $("#id_matricula_student").attr('hidden', false)
                            var dados = anolectivo_ativo + "," + selectedUserId;
                            id_matricula.prop('hidden', false);


                            listabottun +=
                                "<li class='item' style='margin-light:2px'> <button onmouseout='close_filtroEmolemento()' onmouseover='lista_filtro_emolumento(" +
                                item.id + ")' id='anolectivo" + item.id + "' value='" + item.id +
                                "' type='button' onclick='listItens(" + item.id +
                                ")'  style='border-right:rgb(191, 197, 252) 1px solid; border-top:rgb(191, 197, 252) 1px solid; border-top-right-radius: 8px' class='nav-link btn-outline-dark ativo '>" +
                                item.current_translation.display_name + "</button>"
                            listabottun += "</li>"
                        } else {
                            $("#id_matricula_student").attr('hidden', true)
                            id_matricula.prop('hidden', true);
                            listabottun +=
                                "<li class='item' style='margin-light:2px'> <button onmouseout='close_filtroEmolemento()' onmouseover='lista_filtro_emolumento(" +
                                item.id + ")' id='anolectivo" + item.id + "' value='" + item.id +
                                "' type='button' onclick='listItens(" + item.id +
                                ")'  style='border-right:rgb(191, 197, 252) 1px solid; border-top:rgb(191, 197, 252) 1px solid; border-top-right-radius: 8px' class='nav-link btn-outline-dark '>" +
                                item.current_translation.display_name + "</button>"
                            listabottun += "</li>"
                        }


                    });
                    $("#listaBotunAnoLectivo").append(listabottun);
                    load == true ? 0 : $("#change-aluno-load").modal('hide');
                    // load==false ?  listItens(resultData[0].id) : 0;
                } else {
                    $("#listaBotunAnoLectivo").empty();
                    $("#id_matricula").attr('hidden', true)
                }
                if (anolectivoFinalista.length > 0) {
                    $.each(anolectivoFinalista, function(indexInArray, item) {

                        listafinalista +=
                            "<li class='item' style='margin-light:2px'> <button  id='studentFinalista'  type='button' onclick='getEmolumentoFinalista(" +
                            item.lective_years_id +
                            ")'  style='border-right:rgb(191, 197, 252) 1px solid; border-top:rgb(191, 197, 252) 1px solid; border-top-right-radius: 8px' class='nav-link btn-outline-dark'>" +
                            item.anoLectivo + " - finalista</button></li>"
                    });
                    $("#listaBotunAnoLectivo").append(listafinalista);
                }
                if (sem_matricula == true) {
                    // anolectivo_ativo=data['anoativo'];
                    listaOutrosBtn +=
                        "<li class='item' style='margin-light:2px'> <button  id='outroEmolumento'  type='button' onclick='getOutrosEmolumentoRequerido()'  style='border-right:rgb(191, 197, 252) 1px solid; border-top:rgb(191, 197, 252) 1px solid; border-top-right-radius: 8px' class='nav-link btn-outline-dark'>Outros</button></li>"
                    $("#listaBotunAnoLectivo").append(listaOutrosBtn);
                }
                if (load == false) {

                    getOutrosEmolumentoRequerido()
                }






                $(".fotoUserFunc").css('background-image', "")
                var jabutiImg = null;
                jabutiImg = new Image();
                jabutiImg.onload = function() {
                    if (data['getInformalionUser'].length > 0) {
                        $(".fotoUserFunc").attr('style', "background-image: url(" + jabutiImg.src + ")")
                    } else {
                        $(".fotoUserFunc").css('background-image', "")
                    }
                }
                if (data['getInformalionUser'].length > 0) {
                    jabutiImg.src = "//{{ $_SERVER['HTTP_HOST'] }}/payments/view-file/attachment/" + data[
                        'getInformalionUser'][0].value;
                }





            })
        }

        function getStudentInfo(userId) {
            // let pdfRoute = ("{{ route('transactions.pdf', 'id_user') }}").replace('id_user', userId);
            let observationsRoute = ("{{ route('transaction_observations.show', 'id_user') }}").replace('id_user', userId);
            // let routeToCreate = ("{{ route('user_requests_create', 'id_user') }}").replace('id_user', selectedUserId);

            // $.get(pdfRoute, function (data) {

            $(".contaCorrte").attr('hidden', false)

            //     document.getElementById('call-pdf').setAttribute('href', pdfRoute);
            // });

            $.get(observationsRoute, function(data) {
                $(".observacao").attr('hidden', false)
                document.getElementById('call-observation').setAttribute('href', observationsRoute);
            });
            // document.getElementById('bnt-new').setAttribute('href', routeToCreate);
        }

        //  Este metodo consulta as propina a se pagar. 
        function getConsultaPropina_apagar(selectedUserId) {
            $.ajax({
                url: "/payments/getConsultaPropina_apagar/" + selectedUserId + "/" + anolectivo_ativo,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {

                ano = null;
                monthOut_Dezembro = [];
                monthJan_Julho = [];
                var artclAtivo = null;
                var vetorArticl = []
                $.each(data['data'], function(key, item) {
                    if (vetorArticl.length > 0) {
                        var found = vetorArticl.find(element => element == item.article_req_id)

                        if (found != undefined) {
                            artclAtivo = false;
                        } else {
                            vetorArticl.push(item.article_req_id);
                            artclAtivo = true;
                        }

                    } else {
                        vetorArticl.push(item.article_req_id);
                        artclAtivo = true;
                    }
                    if (artclAtivo == true) {
                        if (ano == null) {
                            ano = item.article_year
                            item.article_year == 2020 && item.article_month == 3 || item.article_month >=
                                10 && item.article_month <= 12 ? monthOut_Dezembro.push(item
                                    .article_month) : monthJan_Julho.push(item.article_month);

                        } else if (ano != item.article_year) {
                            ano = item.article_year
                            item.article_year == 2020 && item.article_month == 3 || item.article_month >=
                                10 && item.article_month <= 12 ? monthOut_Dezembro.push(item
                                    .article_month) : monthJan_Julho.push(item.article_month);
                        } else if (ano == item.article_year) {
                            ano = item.article_year
                            item.article_year == 2020 && item.article_month == 3 || item.article_month >=
                                10 && item.article_month <= 12 ? monthOut_Dezembro.push(item
                                    .article_month) : monthJan_Julho.push(item.article_month);
                        }
                    }

                });


                monthOut_Dezembro = [...new Set(monthOut_Dezembro)];
                monthJan_Julho = [...new Set(monthJan_Julho)];

                monthJan_Julho.sort();
                monthOut_Dezembro.sort((a, b) => a - b);
            })


        }
        // Neste metodo verifica-se os meses a ser pagos, e os a se pagar se tudo bate,
        //certo o botão para efetuar pagamento aparece. 
        // consulta os emolumentos do aluno de acordo a ano
        function getFiltroEmolumento_student() {
            $.ajax({
                url: "/payments/getFiltroEmolumento_student/" + selectedUserId,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {

                arrylitaFiltro_emolut = data['data']

            })
        }

        // listar os emolumentos filtrados
        function lista_filtro_emolumento(anoLectivo_id) {
            listaEmolemento = "";
            ano_lectivo_activo = anoLectivo_id;

            getExtract();

            $.each(arrylitaFiltro_emolut, function(indexInArray, valueOfElement) {

                if (anoLectivo_id == valueOfElement[1]) {

                    $(".dropdown_menu").empty();
                    ano = anoLectivo_id;
                    $.each(valueOfElement[0], function(index, item) {
                        listaEmolemento += "<li class='mt-2'><button value='" + anoLectivo_id +
                            "' onclick='filtroEmolumento_student(" + item.art_id +
                            ")' style='outline: none' class='dropdown-item'n type='button' >" +
                            item.article_name + "</button>"
                        listaEmolemento += "</li>"

                    });
                    $(".dropdown_menu").append(listaEmolemento)
                    $("#container_menu").attr('hidden', false);
                }
            });


        }

        function close_filtroEmolemento() {
            $("#container_menu").attr('hidden', true);
        }

        function open_emolumento() {
            $("#container_menu").attr('hidden', false);
        }
        // listar os dados de acordo o emolumento filtrado.
        function filtroEmolumento_student(id_art) {
            if (anolectivo_ativo == null) {
                anolectivo_ativo = ano
            } else {
                $("#anolectivo" + anolectivo_ativo).attr('class', '')
                $("#anolectivo" + anolectivo_ativo).attr('class', 'nav-link btn-outline-dark')
                anolectivo_ativo = ano
            }
            $("#anolectivo" + ano).attr('class', '')
            $("#anolectivo" + ano).attr('class', 'nav-link btn-outline-dark ativo')

            var dados = anolectivo_ativo + "," + selectedUserId;


            // $("#bnt-new").css('visibility','visible')
            $("#bnt-new").attr('hidden', false)
            $.ajax({
                url: "/payments/filtroEmolumento_student/" + id_art + "/" + selectedUserId + "/" + anolectivo_ativo,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {

                if (data['data'] == false) {
                    $('#container').empty()
                    $('#group').show()
                    var mensagem = "<h5>Nenhum registo</h5>"
                    $("#container").append(mensagem);
                } else {
                    getConsultaPropina_apagar(selectedUserId);

                    $('#container').empty()
                    $('#group').hide(); //Esconder a tabela principal antes de chamar a dos resultados
                    $('#container').html(data
                        .html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)
                    verMonth_serPagos()
                    verDeleteAllArticl()
                }
            })
        }

        function verMonth_serPagos() {
            var qtdCheckBox = $("#qdtIndex").val()
            listaChecado = [];
            getMonthOut_Dez = [];
            getMonthsJan_Julho = [];
            getboolenOut_Dez = null;
            getboolenJan_Julho = null;
            var not_month = null

            $(".checagem_month").change(function() {
                var checagem_month = $(this).val()
                var showBottum = null
                var hideBottum = null;

                var year = $(this).attr("data-year");
                var month = $(this).attr("data-id");
                var botaoNa_linha = $(this).attr("data-columns");

                var article = $(this).val();
                var idTransacion = $(this).attr("data-idTransacion");
                if (month == "") {

                    if ($(this).is(":checked")) {
                        not_month = 1;
                        listaChecado.push(botaoNa_linha)
                        if (getMonthOut_Dez.length == monthOut_Dezembro.length && getMonthsJan_Julho.length > 0) {
                            getboolenOut_Dez == true && getboolenJan_Julho == true ? showBottum = true :
                                showBottum = false;
                        }
                        getMonthOut_Dez.length > 0 && getMonthsJan_Julho.length == 0 ? getboolenOut_Dez == true ?
                            showBottum = true : showBottum = false : 0;
                        monthOut_Dezembro.length == 0 && monthJan_Julho.length > 0 ? getboolenJan_Julho == true ?
                            showBottum = true : showBottum = false : 0;
                        getMonthOut_Dez.length == 0 && getMonthsJan_Julho.length == 0 ? showBottum = true : 0;
                    } else {
                        not_month = 0;
                        if (getMonthOut_Dez.length == monthOut_Dezembro.length && getMonthsJan_Julho.length > 0) {
                            getboolenOut_Dez == true && getboolenJan_Julho == true ? hideBottum = true :
                                hideBottum = false;
                        }
                        getMonthOut_Dez.length > 0 && getMonthsJan_Julho.length == 0 ? getboolenOut_Dez == true ?
                            hideBottum = true : hideBottum = false : 0;
                        monthOut_Dezembro.length == 0 && monthJan_Julho.length > 0 ? getboolenJan_Julho == true ?
                            hideBottum = true : hideBottum = false : 0;
                    }

                } else {
                    // estrutura de condição que verficar quando se trata de de mese ou propinas 
                    if ($(this).is(":checked")) {
                        listaChecado.push(botaoNa_linha)
                        month == 3 && year == 2020 || month >= 10 && month <= 12 ? getMonthOut_Dez.push(month) :
                            getMonthsJan_Julho.push(month);
                        getMonthOut_Dez.sort((a, b) => a - b);
                        getMonthsJan_Julho.sort();

                        $.each(getMonthOut_Dez, function(index, item) {
                            item == monthOut_Dezembro[index] ? getboolenOut_Dez = true : getboolenOut_Dez =
                                false;
                        });
                        $.each(getMonthsJan_Julho, function(index, item) {
                            item == monthJan_Julho[index] ? getboolenJan_Julho = true : getboolenJan_Julho =
                                false;
                        });

                        if (getMonthOut_Dez.length == monthOut_Dezembro.length && getMonthsJan_Julho.length > 0) {
                            getboolenOut_Dez == true && getboolenJan_Julho == true ? showBottum = true :
                                showBottum = false;
                        }
                        getMonthOut_Dez.length > 0 && getMonthsJan_Julho.length == 0 ? getboolenOut_Dez == true ?
                            showBottum = true : showBottum = false : 0;
                        monthOut_Dezembro.length == 0 && monthJan_Julho.length > 0 ? getboolenJan_Julho == true ?
                            showBottum = true : showBottum = false : 0;
                        const NOT_PAGO = verifyStatusNotPago(month);
                        if (NOT_PAGO) showBottum = true;
                    } else {
                        month == 3 && year == 2020 || month >= 10 && month <= 12 ? $.each(getMonthOut_Dez, function(
                            index, element) {
                            element == month ? getMonthOut_Dez.splice([index], 1) : 0;
                        }) : $.each(getMonthsJan_Julho, function(index, element) {
                            element == month ? getMonthsJan_Julho.splice([index], 1) : 0;
                        });
                        getMonthOut_Dez.sort((a, b) => a - b);
                        getMonthsJan_Julho.sort();
                        $.each(getMonthOut_Dez, function(index, item) {
                            item == monthOut_Dezembro[index] ? getboolenOut_Dez = true : getboolenOut_Dez =
                                false;
                        });
                        $.each(getMonthsJan_Julho, function(index, item) {
                            item == monthJan_Julho[index] ? getboolenJan_Julho = true : getboolenJan_Julho =
                                false;
                        });

                        $.each(listaChecado, function(index, element) {
                            botaoNa_linha == element ? listaChecado.splice([index], 1) : 0;
                        });

                        if (getMonthOut_Dez.length == monthOut_Dezembro.length && getMonthsJan_Julho.length > 0) {
                            getboolenOut_Dez == true && getboolenJan_Julho == true ? hideBottum = true :
                                hideBottum = false;
                        }
                        getMonthOut_Dez.length > 0 && getMonthsJan_Julho.length == 0 ? getboolenOut_Dez == true ?
                            hideBottum = true : hideBottum = false : 0;
                        monthOut_Dezembro.length == 0 && monthJan_Julho.length > 0 ? getboolenJan_Julho == true ?
                            hideBottum = true : hideBottum = false : 0;
                        not_month == 1 && getMonthOut_Dez.length == 0 && getMonthsJan_Julho.length == 0 ?
                            showBottum = true : showBottum = false;
                        const NOT_PAGO = verifyStatusNotPago(month);
                        if (!NOT_PAGO) showBottum = false;
                    }
                }




                // estrutura de condição que mostra o botão.
                if (showBottum == true) {
                    for (let index = 0; index <= qtdCheckBox; index++) {
                        if (index == botaoNa_linha) {
                            $("#checado" + botaoNa_linha).attr('hidden', false)
                            $("#btn-referenciaEmolumento" + botaoNa_linha).attr('hidden', false)
                        } else {
                            $("#checado" + index).attr('hidden', true);
                            $("#btn-referenciaEmolumento" + index).attr('hidden', true)
                        }
                    }
                } else {
                    for (let index = 0; index <= qtdCheckBox; index++) {
                        $("#checado" + index).attr('hidden', true);
                        $("#btn-referenciaEmolumento" + index).attr('hidden', true);
                    }
                }

                if (hideBottum == true) {
                    for (let index = 1; index <= qtdCheckBox; index++) {
                        if (index == listaChecado[listaChecado.length - 1]) {
                            $("#checado" + listaChecado[listaChecado.length - 1]).attr('hidden', false)
                            $("#btn-referenciaEmolumento" + listaChecado[listaChecado.length - 1]).attr('hidden',
                                false)
                        } else {
                            $("#checado" + index).attr('hidden', true)
                            $("#btn-referenciaEmolumento" + index).attr('hidden', true)
                        }

                    }
                } else if (hideBottum == false) {
                    for (let index = 1; index <= qtdCheckBox; index++) {
                        $("#checado" + index).attr('hidden', true);
                        $("#btn-referenciaEmolumento" + index).attr('hidden', true);
                    }
                }



            });

            $(".checagem").change(function() {
                var checagem = $(this).val()
                var botaoNa_linha = $(this).attr("data-columns");
                var showBott = null;
                var hideBott = null;
                var article = $(this).val();
                var idTransacion = $(this).attr("data-idTransacion");

                if ($(this).is(":checked")) {
                    listaChecado.push(botaoNa_linha)
                    getMonthOut_Dez.length > 0 && getMonthsJan_Julho.length > 0 && getboolenJan_Julho == true &&
                        getboolenOut_Dez == true ? showBott = true : showBott = false;
                    getMonthOut_Dez.length > 0 && getMonthsJan_Julho.length == 0 && getboolenOut_Dez == true ?
                        showBott = true : 0;
                    getMonthOut_Dez.length == 0 && getMonthsJan_Julho.length == 0 ? showBott = true : 0;

                    monthOut_Dezembro.length == 0 && monthJan_Julho.length > 0 ? getboolenJan_Julho == true ?
                        showBott = true : showBott = false : 0;


                } else {
                    $.each(listaChecado, function(index, element) {
                        botaoNa_linha == element ? listaChecado.splice([index], 1) : 0;
                    });

                    getMonthOut_Dez.length > 0 && getMonthsJan_Julho.length > 0 && getboolenJan_Julho == true &&
                        getboolenOut_Dez == true ? hideBott = true : 0;
                    getMonthOut_Dez.length > 0 && getMonthsJan_Julho.length == 0 && getboolenOut_Dez == true ?
                        hideBott = true : 0;
                    getMonthOut_Dez.length == 0 && getMonthsJan_Julho.length == 0 ? hideBott = true : 0;
                    not_month == 1 && getMonthOut_Dez.length == 0 && getMonthsJan_Julho.length == 0 ? hideBott =
                        true : 0;
                    monthOut_Dezembro.length == 0 && monthJan_Julho.length > 0 ? getboolenJan_Julho == true ?
                        hideBott = true : 0 : 0;
                }



                if (showBott == true) {
                    for (let index = 1; index <= qtdCheckBox; index++) {
                        if (index == botaoNa_linha) {
                            $("#checado" + botaoNa_linha).attr('hidden', false)
                            $("#btn-referenciaEmolumento" + botaoNa_linha).attr('hidden', false)
                        } else {
                            $("#checado" + index).attr('hidden', true);
                            $("#btn-referenciaEmolumento" + index).attr('hidden', true);
                        }
                    }
                } else {
                    for (let index = 1; index <= qtdCheckBox; index++) {
                        $("#checado" + index).attr('hidden', true);
                        $("#btn-referenciaEmolumento" + index).attr('hidden', true);
                    }
                }

                if (hideBott == true) {
                    for (let index = 1; index <= qtdCheckBox; index++) {
                        if (index == listaChecado[listaChecado.length - 1]) {
                            $("#checado" + listaChecado[listaChecado.length - 1]).attr('hidden', false)
                            $("#btn-referenciaEmolumento" + listaChecado[listaChecado.length - 1]).attr('hidden',
                                false)
                        } else {
                            $("#checado" + index).attr('hidden', true);
                            $("#btn-referenciaEmolumento" + index).attr('hidden', true);
                        }
                    }
                } else if (hideBott == false) {
                    for (let index = 1; index <= qtdCheckBox; index++) {
                        $("#checado" + index).attr('hidden', true);
                        $("#btn-referenciaEmolumento" + index).attr('hidden', true);
                    }
                }
            });

        }

        function verifyStatusNotPago(month) {
            const status = $('#status_' + month).html().trim();
            return status != 'PAGO';
        }

        function verDeleteAllArticl() {
            listaArticlDeleted = []
            listaArticlReferencia = []
            $(".checagemdeleteArticl").change(function() {
                var article = $(this).val();
                var status = $(this).attr("data-status");
                var idTransacion = $(this).attr("data-idTransacion");

                if ($(this).is(":checked")) {
                    listaArticlDeleted.push(article)
                    deleteAllArticl.push(status)
                    listaArticlReferencia.push(article + "," + idTransacion + "@");

                } else {
                    $.each(listaArticlDeleted, function(index, item) {
                        if (item == article) {
                            listaArticlDeleted.splice([index], 1);
                            deleteAllArticl.splice([index], 1);
                            listaArticlReferencia.splice([index], 1);
                        }
                    });
                }
                var found = deleteAllArticl.find(element => element == "partial")
                if (found == undefined && deleteAllArticl.length > 0) {
                    $("#btnDeleteAllarticl").attr('hidden', false)

                } else {
                    $("#btnDeleteAllarticl").attr('hidden', true)
                }
                referencia_emolument.val(listaArticlReferencia);

            });
        }

        function get_bolseiro(student_id) {



            $.ajax({
                url: "/gestao-academica/student-scholarship/" + student_id,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {

                if (data[0] == 1) {

                    if (data[1] == 0) {
                        $("#bolseiro").text("Estudante bolseiro [ Sem desconto]");
                    } else {
                        $("#bolseiro").text("Estudante bolseiro [ Desconto " + data[1] + "%]");
                    }
                } else {
                    $("#bolseiro").text("");
                }
            });

        }
    </script>
@endsection
