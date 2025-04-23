@section('title', __('RH-recurso humanos'))
@extends('layouts.backoffice')
@section('styles')
    @parent
@endsection
@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <style>
        .list-group li button {
            border: none;
            background: none;
            outline-style: none;
            transition: all 0.5s;
        }

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

        .fotoUserFunc {
            border-radius: 50%;
            background-color: #c4c4c4;
            background-size: contain;
            /* background-repeat: no-repeat; */
            background-position: 50%;
            width: 150px;
            height: 150px;
            -webkit-filter: brightness(.9);
            filter: brightness(.9);
            border: 5px solid #fff;
            -webkit-transition: all .5s ease-in-out;
            transition: all .5s ease-in-out;
        }

        .modal-body span {
            font-size: 13px;
            color: black;
        }

        .divtable-lista::-webkit-scrollbar {
            width: 8px;
            height: 2px;
            border-radius: 30px;
            box-shadow: inset 20px 20px 60px #bebebe,
                inset -20px -20px 60px #ffffff;
        }

        .divtable-lista::-webkit-scrollbar-track {
            background: #e0e0e0;
            box-shadow: inset 20px 20px 60px #bebebe,
                inset -20px -20px 60px #ffffff;
            border-radius: 30px;
            height: 2px
        }

        .divtable-lista::-webkit-scrollbar-thumb {
            background-color: #343a40;
            border-radius: 30px;
            border: none;
            height: 2px
        }

        .divtable-lista {
            height: 23.1pc;
        }
    </style>



    <div class="content-panel">
        @include('RH::index_menu')

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-6">
                        <h1>{{ $action }}</h1>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </div>

        <p class="btn-menu col-md-0 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
        <div class="content-fluid ml-4 mr-4 mb-5">
            <div class="d-flex align-items-start">
                @include('RH::index_menuStaff')
                <div style="background-color: #f8f9fa" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                    <div class="criarCodigo ">

                        <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                            <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px "
                                class="col-12 m-0 mb-3 "></div>


                            <div class="col-md-12 align-items-end ">
                                <div class="float-right  d-flex flex-row-reverse bd-highlight">
                                    <div class="p-2 bd-highlight">
                                        <h5 class="text-muted text-uppercase"> Associar banco ao funcionário</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                                <div class="col-12 mb-4 ">
                                    {{-- formularios --}}
                                    <form method="POST" action="{{ route('recurso-humano.store-user-banco') }}"
                                        class="pb-4">
                                        @csrf

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputAddress">Funcionário</label>
                                                <select data-live-search="true" class="selectpicker form-control"
                                                    id="funcionario" data-actions-box="false"
                                                    data-selected-text-format="values" name="funcionario" tabindex="-98">
                                                    <option></option>
                                                    @foreach ($users as $element)
                                                        <option value="{{ $element->id }}">{{ $element->name }} -
                                                            {{ $element->email }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="inputAddress">Banco</label>
                                                <select data-live-search="true" class="selectpicker form-control"
                                                    id="banco" data-actions-box="false"
                                                    data-selected-text-format="values" name="banco" tabindex="-98">
                                                    <option></option>
                                                    @foreach ($bancos as $element)
                                                        <option value="{{ $element->id }}">{{ $element->code }} -
                                                            {{ $element->display_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class=" col-md-6 conta" hidden>
                                                <label for="inputAddress">Nº da conta</label>
                                                <input required type="number" min="0" class="form-control"
                                                    name="conta" id="conta" placeholder="Nº de conta">
                                            </div>

                                            <div class="col-md-6 iban" hidden>
                                                <label for="inputAddress">IBAN</label>
                                                <input required type="number" class="form-control " name="iban"
                                                    id="iban" aria-describedby="errorIban" placeholder="IBAN">
                                                <small id="errorIban" class="form-text text-success"></small>
                                            </div>
                                        </div>

                                        <div class="form-row ml-0 mt-1 pl-0 btn-save" hidden>
                                            <div class="form-group mr-3">
                                                <button type="submit" style="background: #2b9fc2"
                                                    class="btn text-white btn-gerarPDF">Gravar</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div hidden class="alert alerta alert-dark" role="alert">

                                    </div>

                                    <table id="banks-table" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Funcionário</th>
                                                <th>E-mail</th>
                                                <th>Banco</th>
                                                {{-- <th>Sigla</th> --}}
                                                {{-- <th>Estado</th> --}}
                                                {{-- <th>@lang('translations.display_name')</th> --}}
                                                <th>@lang('common.created_by')</th>
                                                {{-- <th>@lang('common.updated_by')</th> --}}
                                                <th>@lang('common.created_at')</th>
                                                {{-- <th>@lang('common.updated_at')</th> --}}
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
    </div>

    <!-- Modal  que apresenta a opção de eliminar -->
    <div style="z-index: 999999999" class="modal fade" id="delete-bank" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content mt-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Informação!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>Caro utilizador deseja eliminar este banco?</div>
                    <div class="">
                        <input class="is_force" type="checkbox" name="is_force" id="is_force" value="on" />
                        <label class=" mt-1" for="is_force">Tens certeza que concordas con esta acção</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        id="force-close">Cancelar</button>
                    <button class="btn btn-primary btn-delete-bank-user">Ok</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div style="z-index: 999999999" class="modal fade" id="delete-bank-contrato" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content mt-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Informação!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Caro utilizador deseja eliminar este?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary btn-delete-bank-user-contrato">Ok</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar  -->
    <div style="background: #2222225c; z-index: 9999999;" class="modal fade" id="associar_contrato" tabindex="-1"
        role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered  rounded " role="document">
            <div class="modal-content rounded" style="z-index: 99999;background: transparent;border: none">
                <div class="modal-header bg-white mb-2 rounded">
                    <h4 class="modal-title" id="exampleModalLongTitle"><b>Associar banco ao contrato </b>[ Cargo
                        funcionário ]</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="row col-12 m-0 p-0">
                    <div class="col-md-4 m-0 p-0 pr-2 ">
                        <div class="m-0 p-0 bg-white rounded">
                            <div style="background:#14e396;width: 100%;border-top-left-radius: 3px;border-top-right-radius: 3px;height: 4px;"
                                class="m-0"></div>
                            <div class="modal-header">
                                <h4 style="font-size: 13px;" class="modal-title" id="exampleModalLongTitle">Criar
                                    associação [ Banco e contrato]</h4>
                            </div>

                            <div class="modal-body">
                                <div class="col-12 mb-3 p-0">
                                    <form id="formRoute-Edita-bankContrato" method="POST" action=""
                                        class="pb-3 ">
                                        @csrf
                                        <div class="form-group col-md">
                                            <label for="inputAddress">Cargo com contrato</label>
                                            <select data-live-search="true" class="selectpicker form-control"
                                                id="contato_data" data-actions-box="false"
                                                data-selected-text-format="values" name="contrato_data" tabindex="-98">

                                            </select>
                                        </div>
                                        <div class="form-group col-md mb-4">
                                            <label for="inputAddress">Banco</label>
                                            <select data-live-search="true" class="selectpicker form-control"
                                                id="banco_data" data-actions-box="false"
                                                data-selected-text-format="values" name="banco_data" tabindex="-98">

                                            </select>
                                        </div>
                                        <div class="form-group col-md mt-4 mb-3">
                                            <button type="submit" class="btn btn-success"
                                                id="gravar_dados">Associar</button>
                                        </div>
                                        <div class="form-group col-md">
                                            <small class="text-muted mt-3 border-top pt-3">
                                                Caro utilizador para associar o banco (<i>Conta bancária</i>) a um
                                                respectivo contrato, por favor selecione o cargo com contrato e o banco.
                                            </small>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col m-0 p-0 pl-2">
                        <div class="m-0 p-0 bg-white rounded">
                            <div style="background:#20c7f9;width: 100%;border-top-left-radius: 3px;border-top-right-radius: 3px;height: 4px;"
                                class="m-0"></div>
                            <div class="modal-header">
                                <h4 style="font-size: 13px;" class="modal-title" id="exampleModalLongTitle">Lista de
                                    contrato com banco</h4>
                            </div>

                            <div class="divtable-lista table-responsive pb-2  modal-body">
                                <div hidden class="alert alerta-contra-excluir alert-dark" role="alert"></div>
                                <div class="col-12 mb-3 p-0">
                                    <table id="contrato-banks-table" class="table table-striped table-hover">
                                        <thead>

                                            <tr>
                                                <th>#</th>
                                                <th>Funcionário</th>
                                                <th>Banco</th>
                                                <th>Cango[contrato]</th>
                                                <th>Banco a ser usado [processar SL]</th>
                                                <th>@lang('common.created_by')</th>
                                                <th>@lang('common.created_at')</th>
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
    </div>

@endsection
@section('scripts')
    @parent
    <script>
        let force = document.querySelector(".is_force");

        $(document).ajaxStart(function(e) {
            $.ajaxSetup({
                data: {
                    is_force: force.value
                }
            });
        });

        force.addEventListener('change', function(e) {
            force.value = force.value == "on" ? "" : "on";
        });

        // $('#delete-bank').on('hidden.bs.modal', function(e) {
        //     let force = document.querySelector(".is_force");
        //     force.value = "";
        //     force.removeAttribute('checked');
        // })

        $("#banco").change(function(e) {
            var banco = $(this).val();
            banco == "" ? $(".conta").prop('hidden', true) : $(".conta").prop('hidden', false);
            banco == "" ? $(".iban").prop('hidden', true) : 0;
            banco == "" ? $(".btn-save").prop('hidden', true) : 0;
            $("#conta").val(null);
            $("#iban").val(null)
        });
        $('#conta').on('blur', function() {
            var num_conta = $(this).val();
            var numero = "conta" + "," + num_conta;
            $.ajax({
                url: 'recuso-humano-validationContaBancaria-Or-validationIBAN/' + numero,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {
                if (data == true) {
                    $("#conta").removeClass('is-invalid');
                    $("#conta").addClass('is-valid');
                    $(".iban").prop('hidden', false)

                } else {
                    $("#conta").removeClass('is-valid');
                    $("#conta").addClass('is-invalid');
                    $(".iban").prop('hidden', true)
                }


            })
        });

        $("#iban").on('blur', function() {
            var count = $(this).val().length;
            var er = /[^0-9]/;
            er.lastIndex = 0;
            var campo = $(this);
            var numero = $(this).val();
            // numero=  numero[0].toUpperCase() + numero.slice(1); 
            console.log(numero.length);
            if (er.test(numero)) {
                campo.value = "";
            }
            if (count === 21) {
                $("#errorIban").text("")
                numero = "IBAN" + "," + numero;
                $.ajax({
                    url: 'recuso-humano-validationContaBancaria-Or-validationIBAN/' + numero,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    if (data == true) {
                        $("#iban").removeClass('is-invalid');
                        $("#iban").addClass('is-valid');
                        $(".btn-save").prop('hidden', false)
                    } else {
                        ("#iban").removeClass('is-valid');
                        $("#iban").addClass('is-invalid');
                        $(".btn-save").prop('hidden', true)
                    }


                })

            } else {
                $("#iban").addClass('is-invalid');
                $("#iban").removeClass('is-valid');
                $("#errorIban").text("A quantidade de caracteres do IBAN não comresponde")
            }

        })



        // $(function () {
        function getLista_bank_user() {
            $('#banks-table').DataTable({
                ajax: '{!! route('recurso-humano.ajax-user-banco') !!}',
                buttons: [
                    'colvis',
                    'excel'
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'name',
                        name: 'fullName.value',
                    }, {
                        data: 'email',
                        name: 'email'
                    }, {
                        data: 'banks',
                        name: 'banks',
                        orderable: false,
                        searchable: false
                    },
                    // {
                    //     data: 'banco_sigla',
                    //     name: 'banco_sigla'
                    // }, 
                    // {
                    //     data: 'status',
                    //     name: 'status',
                    // }, 
                    {
                        data: 'created_by',
                        name: 'created_by',
                    },
                    // {
                    //     data: 'updated_by',
                    //     name: 'updated_by',
                    //     visible: false
                    // }, 
                    {
                        data: 'created_at',
                        name: 'created_at',
                    },
                    {
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                }
            });
        }
        getLista_bank_user()
        // });

        // Delete confirmation modal onkeydown=somenteNumeros(this) onkeyup=somenteNumeros(this)
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
