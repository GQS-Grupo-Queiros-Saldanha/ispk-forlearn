<title>Requerimentos | forLEARN® by GQS</title>
@extends('layouts.backoffice')
@section('styles')
@parent
<style>
    .red {
        background-color: red !important;
    }

    .dt-buttons {
        float: left;
        margin-bottom: 20px;
    }

    .dataTables_filter label {
        float: right;
    }


    .dataTables_length label {
        margin-left: 10px;
    }

    .casa-inicio {}

    .div-anolectivo {
        width: 300px;

        padding-right: 0px;
        margin-right: 15px;
    }

    #table-merito form {
        margin: 0px;
        display: inline;
    }

    #Modalconfirmar form input,
    #user_id {
        transform: scale(0);
    }

    table form {
        margin-bottom: 0px;
        display: contents;
    }

    #ModalWord form {
        display: flex;
    }

    #request-table tbody tr td:nth-child(9) {
        display: flex;
        align-content: center;
        justify-content: center;
    }

    #request-table thead tr th:nth-child(9) {
        text-align: center;
    }
</style>
@endsection

<div class="modal fade" id="Modalconfirmar" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="Modalconfirmar" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 10px;">

            <div class="modal-body">
                <center><i class="fas fa-trash-alt btn-danger"
                        style="font-size: 30px;padding: 30px;border-radius: 60px;color:white;" aria-hidden="true"></i>
                </center>
                <p style="font-size: 25px;text-align: center;">
                    Tens a certeza que desejas excluir ?
                </p>
            </div>
            <div class="modal-footer">


                {!! Form::open(['route' => ['requerimento.delete_doc']]) !!}

                <input type="number" name="id" id="input1">
                <input type="text" name="type" id="type">


                <button type="submit" class="btn btn-danger" style="border-radius:5px;">Eliminar</button>
                {!! Form::close() !!}
                <a href="#" type="button" data-dismiss="modal" class="btn btn-info"
                    style="border-radius:5px;">Cancelar</a>
            </div>
        </div>
    </div>
</div>


@section('content')
<div class="content-panel" style="padding: 0;">
    @include('Avaliations::requerimento.navbar.navbar')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class=" float-right">
                        <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                            <li class="breadcrumb-item active" aria-current="page">Requerimentos</li>

                        </ol>
                    </div>
                </div>
            </div>


            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>@lang('REQUERIMENTOS')</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right div-anolectivo">
                        <label>Selecione o ano lectivo</label>
                        <br>
                        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm"
                            style="width: 100%; !important">
                            @foreach ($lectiveYears as $lectiveYear)
                                @if ($lectiveYearSelected == $lectiveYear->id)
                                    <option value="{{ $lectiveYear->id }}" selected>
                                        {{ $lectiveYear->currentTranslation->display_name }}
                                    </option>
                                @else
                                    <option value="{{ $lectiveYear->id }}">
                                        {{ $lectiveYear->currentTranslation->display_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">

                    <div class="row">

                        <div class="col-6 div-anolectivo">
                            <div class="form-group col">
                                <label>Tipo de documento</label>

                                <select name="requerimento_tipo" id="requerimento_tipo"
                                    class="selectpicker form-control form-control-sm" data-live-search="true"
                                    style="width: 100%; !important">
                                    <option value="0" selected>Nenhum seleccionado</option>
                                    <option value="9">Anulação de Matrícula</option>
                                    <option value="18">Carta de recomendação</option>
                                    <option value="1">Certificado</option>
                                    
                                    <option value="6">Certificado de mérito</option>
                                    <option value="2">Declaração com notas</option>
                                    <option value="13">Declaração com Notas de Exame de Acesso</option>
                                    <!--<option value="3">Declaração sem notas</option>-->
                                    <option value="8">Declaração de Frequência</option>
                                    <option value="10">Declaração de Fim de Curso</option>
                                    <option value="7">Diploma</option>
                                    <option value="5">Exame especial</option>
                                    <option value="4">Exame de recurso</option>
                                    <option value="12">Mudança de turma</option>
                                    <option value="14">Pedido de transferência (de entrada no {{$institution->abrev}})</option>
                                    <option value="15">Pedido de transferência (de saída do {{$institution->abrev}})</option>
                                    <option value="16">Percurso académico</option>
                                    <option value="18"> Prova parcelar (2ª chamada)</option>
                                    <option value="17">Solicitação de Estágio</option>
                                    <option value="20">Marcação de Revisão de prova</option>
                                    <option value="21">Defesa Extraordinaria</option>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">




                    <div class="card">
                        {{-- TABELA 1 --}}
                        <div class="card-body c1">
                            <table id="request-table" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nº de documento</th>
                                        <th>Estudante</th>
                                        <th>email</th>
                                        <th>Tipo de documento</th>
                                        <th>Valor</th>
                                        <th>Pagamento</th>
                                        <th>Data</th>
                                        <th>Visualizar</th>
                                        <th>Actividades</th>

                                    </tr>
                                </thead>
                            </table>
                        </div>
                        {{-- TABELA 2 --}}
                        <div class="card-body c2" style="display: none;">
                            <table id="table-merito" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Estudante</th>
                                        <th>email</th>
                                        <th>Ano</th>
                                        <th>Cargo</th>
                                        <th>Actividades</th>

                                    </tr>
                                </thead>
                            </table>
                        </div>

    
                        <div class="card-body c4" style="display: none;">
                            <table id="table-mundanca-turma" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Estudante</th>
                                        <th>email</th>
                                        <th>Turma anterior</th>
                                        <th>Turma nova</th>
                                        <th>Estado de pagamanto </th>
                                        <th>Estado de mudança</th>
                                        <th>Actividades</th>
                                    </tr>
                                </thead>
                            </table>
                            
                        </div>
    
                    </div>
                </div>
    
            </div>
        </div>
    </div>
    @endsection

    
    @section('scripts')
    @parent
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        // Quando o tipo de avaliação for alterada

        $(".modal-footer form div").hide();

        function pegar(element) {

            var id = $(element).attr("data");
            var type = $(element).attr("data-type");

            id = parseInt(id);
            $("#input1").val(id);
            $("#type").val(type);

        }

        function word(element) {
            var user = $(element).attr("data-user");

            user = parseInt(user);
            $("#user_id").val(user);
            get_word(user);

        }

        function get_word(word) {

            $.ajax({
                url: "/avaliations/get_word/" + word,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function (data) {
                $("#word").val(data);
                $("#word_number").text("Folha nº " + data);
            });
        }

        function get_requerimentos(ano, tipo) {


            if (tipo == 0) {

                var AnoDataTable = $('#request-table').DataTable({
                    ajax: {
                        "url": "/avaliations/my_articles/" + ano + "," + tipo,
                        "type": "GET"
                    },
                    destroy: true,
                    buttons: [
                        'colvis',
                        'excel'
                    ],

                    columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'code',
                        name: 'code',
                        searchable: true
                    },
                    {
                        data: 'nome_estudante',
                        name: 'nome_estudante',
                        searchable: true
                    },
                    {
                        data: 'email',
                        name: 'email',
                        searchable: true
                    },
                    {
                        data: 'type',
                        name: 'type',
                        searchable: true
                    },
                    {
                        data: 'base_value',
                        name: 'base_value',
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: true
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: true
                    },
                    {
                        data: 'doc',
                        name: 'doc',
                        searchable: true
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: true
                    }

                    ],

                    "lengthMenu": [
                        [10, 50, 100, 50000],
                        [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    }
                });

            } else {
                var AnoDataTable = $('#request-table').DataTable({
                    ajax: {
                        "url": "/avaliations/my_articles/" + ano + "," + tipo,
                        "type": "GET"
                    },
                    destroy: true,
                    buttons: [
                        'colvis',
                        'excel'
                    ],

                    columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'code',
                        name: 'code',
                        searchable: true
                    },
                    {
                        data: 'nome_estudante',
                        name: 'nome_estudante',
                        searchable: true
                    },
                    {
                        data: 'email',
                        name: 'email',
                        searchable: true
                    },
                    {
                        data: 'type',
                        name: 'type',
                        searchable: true
                    },
                    {
                        data: 'base_value',
                        name: 'base_value',
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: true
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: true
                    },
                    {
                        data: 'doc',
                        name: 'doc',
                        searchable: true
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: true
                    }

                    ],

                    "lengthMenu": [
                        [10, 50, 100, 50000],
                        [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    }
                });
            }
        }


        function pauta_publica() {


            var AnoDataTable = $('#table-merito').DataTable({
                ajax: {
                    "url": "/avaliations/my_articles/" + 0 + "," + 6,
                    "type": "GET"
                },
                destroy: true,
                buttons: [
                    'colvis',
                    'excel'
                ],

                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nome_estudante',
                    name: 'nome_estudante',
                    searchable: true
                },
                {
                    data: 'email',
                    name: 'email',
                    searchable: true
                },
                {
                    data: 'ano',
                    name: 'ano',
                    searchable: true
                },
                {
                    data: 'tipo',
                    name: 'tipo',
                    searchable: true
                },
                {
                    data: 'actions',
                    name: 'actions',
                    searchable: true
                }

                ],

                "lengthMenu": [
                    [10, 50, 100, 50000],
                    [10, 50, 100, "Todos"]
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                }
            });
        }

        pauta_publica();


        function tabela_mudanca() {


            var AnoDataTable = $('#table-mundanca-turma').DataTable({
                ajax: {
                    "url": "/avaliations/my_articles/" + 0 + "," + 12,
                    "type": "GET"
                },
                destroy: true,
                buttons: [
                    'colvis',
                    'excel'
                ],
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'student_name',
                    name: 'student_name',
                    searchable: true
                },
                {
                    data: 'email',
                    name: 'email',
                    searchable: true
                },
                {
                    data: 'turma_antiga',
                    name: 'turma_antiga',
                    searchable: true
                },
                {
                    data: 'turma_nova',
                    name: 'turma_nova',
                    searchable: true
                },
                {
                    data: 'status',
                    name: 'status',
                    searchable: true
                },
                {
                    data: 'status_change',
                    name: 'status_change',
                    searchable: true
                },
                {
                    data: 'actions',
                    name: 'actions',
                    searchable: true
                }

                ],

                "lengthMenu": [
                    [10, 50, 100, 50000],
                    [10, 50, 100, "Todos"]
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                }
            });
        }



        get_requerimentos($("#lective_year").val(), $("#requerimento_tipo").val());



        $("#lective_year").change(function () {

            $('#request-table').hide();
            get_requerimentos($(this).val(), $("#requerimento_tipo").val());
            $('#request-table').show();

        });


        $("#requerimento_tipo").change(function () {
            // Oculta todas as tabelas e seções por padrão
            $('#request-table, #table-merito, .c1, .c2, .c4').hide();

            // Checa se o valor é 6 e exibe apenas o conteúdo relevante para esse caso
            if ($(this).val() == 6) {
                pauta_publica();
                $('#table-merito, .c2').show();
            }
            // Checa se o valor é 12 e exibe apenas o conteúdo relevante para esse caso
            else if ($(this).val() == 12) {
                tabela_mudanca();
                $('#table-mundanca-turma, .c4').show();
            }
            // Caso contrário, exibe o request-table e .c1
            else {
                get_requerimentos($("#lective_year").val(), $(this).val());
                $('#request-table, .c1').show();
            }
        });

    </script>
    @endsection