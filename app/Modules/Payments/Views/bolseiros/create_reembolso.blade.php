<title>Reembolsos | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Criar reembolsos')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Reembolsos</li>
@endsection
@section('styles-new')
    @parent
    <style>
        .list-group li button {
            border: none;
            background: none;
            outline-style: none;
            transition: all 0.5s;
        }

        a: {}

        .list-group li button:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            font-weight: bold
        }

        .subLink {
            list-style: none;
            transition: all 0.5s;
            border-bottom: none;
        }

        .subLink:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            border-bottom: #dfdfdf 1px solid;
        }

        button#create:hover {
            color: black;
        }
    </style>
@endsection
@section('body')
    {!! Form::open(['route' => ['reembolsos.store']]) !!}
    <div class="card">
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active show " id="language1">

                    <div class="row">
                        <div class="form-group col-4">
                            <label for="users">Estudante</label>
                            <input class="form-control" type="text"
                                value="{{ isset($users->full_name) ? $users->full_name : $users->name }} #{{ $users->matriculation }} {{ $users->email }} "
                                id="users" disabled>
                        </div>
                        <div class="form-group col-4">
                            <label for="mode">Saldo em carteira actual</label>
                            <input class="form-control" type="text"
                                value="{{ number_format($users->credit_balance, 2, ',', '.') }} kz" disabled>
                        </div>
                        <div class="form-group col-4" hidden>
                            <label for="users">_</label>
                            <input class="form-control" name="users" type="number" value="{{ $id }}"
                                id="users" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="mode">Valor</label>
                            <input class="form-control" name="value" id="value" onkeypress="somenteNumeros(this)"
                                onkeydown="somenteNumeros(this)" onkeyup="somenteNumeros(this)" type="number"
                                min="1" max="{{ $users->credit_balance }}" placeholder='Digite o montante' required>
                            <small style="font-weight: bold;position: fixed;transform: translateY(55px);" id="valor1"
                                class="form-text text-muted pt-0 mt-0 pl-3"></small>
                        </div>
                        <div class="form-group col-4">
                            <label for="mode">Método</label>
                            <select name="mode" id="mode" class="selectpicker form-control form-control-sm"
                                data-live-search="true" style="width: 100%; !important">
                                <option value="1" selected>Transferência</option>
                                <option value="2">Depósito</option>
                            </select>
                        </div>

                        <div class="form-group col-4">
                            <label for="date">Reembolsado a</label>
                            <input class="form-control" name="date" type="date" id="date" required>
                        </div>

                        <div class="form-group col-4">
                            <label for="reference">Referência</label>
                            <input class="form-control" name="reference" type="text" id="reference"
                                onkeyup="getInfoReferences(this)" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="bank">Banco</label>
                            <input class="form-control" name="bank" type="text" id="bank" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="iban">IBAN / nº de conta</label>
                            <input class="form-control is-invalide" name="iban" type="number" id="iban" required>
                        </div>
                        <div class="form-group col-12">
                            <label for="observation">Observação</label>
                            <textarea class="form-control" name="observation" type="text" id="observation" rows="10" required></textarea>
                        </div>
                        <div class="form-group col-2">
                            <button class="form-control btn btn-outline-success" name="create" type="submit"
                                id="create">
                                <i class="fa fa-plus"></i> Reembolsar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@section('models')
    <!-- Modal para verificar se a referência existe -->
    <div class="modal fade modal_Referencia" id="modal_Referencia" tabindex="-1" role="dialog" data-bs-backdrop="static"
        aria-labelledby="modal_Referencia" aria-hidden="true">
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
                    <a style="border-radius: 6px; background:#000000" target="_blank"
                        class="btn btn-lg text-white btn-submeter btn-get-recibo"><i class="fa fa-file-pdf"></i>
                        Recibo</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-new')
    @parent
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <script>
        function somenteNumeros(num) {
            var er = /[^0-9.]/;
            er.lastIndex = 0;
            var campo = num;
            if (er.test(campo.value)) {
                campo.value = "";
            }
        }

        $("#value").keyup(function(e) {
            var valor = $("#value").val();
            var er = /[^0-9.]/;
            er.lastIndex = 0;
            if (valor == '') {
                $("#valor1").text("")
            } else {
                if (er.test($("#value").val())) {
                    $("#value").val("");
                } else {
                    valor = Number.parseFloat($("#value").val())
                    $("#valor1").html(valor.toLocaleString('pt-br', {
                        minimumFractionDigits: 2
                    }) + " <span>Kz</span>")
                }

            }
        });

        function getInfoReferences(reference) {
            $.ajax({
                method: "GET",
                url: "/pt/payments/reference_get_origem_reembolsos/" + reference.value
            }).done(function(info) {


                if (info["recibo"] != null) {

                    $(".modal_Referencia").modal('show');


                    $(".modal_Referencia .btn-get-recibo").attr('href', "/pt/payments/bolseiros/reembolsos/pdf/" +
                        info["repayment"]);

                    // Adicionar o texto #fcff00

                    $(".modal_Referencia #texto-reference").html("Caro " + info["nome"] +
                        ", forLEARN detectou a Referência:<span class='text-warning' style='font-size: 23px;'> " +
                        reference.value +
                        "</span> no recibo Nº " + info["recibo"] + "");
                }

            });


        }
    </script>
@endsection
