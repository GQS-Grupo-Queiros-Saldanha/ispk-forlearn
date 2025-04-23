<title>Orçamentos | forLEARN® by GQS</title>
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
    </style>

    <!-- Modal para excluir Orçamentos -->


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


                    {!! Form::open(['route' => ['budget.delete']]) !!}
                    {{ Form::bsCustom('id', $budget->name ?? null, ['type' => 'number']) }}

                    <button type="submit" class="btn btn-danger" style="border-radius:5px;">Eliminar</button>
                    {!! Form::close() !!}
                    <a href="#" type="button" data-dismiss="modal" class="btn btn-info"
                        style="border-radius:5px;">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para criar um novo capítulo --}}

    <div class="modal fade bd-example-modal-lg" id="modal-chapter" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="    border-radius: 10px;">
                <h1 class="titulo-capitulo mt-3 ml-3"></h1>
                <br>
                {!! Form::open(['route' => ['budget_chapter.store']]) !!}

                <div class="col-12">
                    <div class="row">
                        <div class="form-group col-12">


                            {{-- Formulários para orçamentos --}}
                            <div class="col-12">
                                <div class="row">
                                    <div class="form-group col-6">
                                        {{ Form::bsCustom('name', null, ['type' => 'text', 'placeholder' => '', 'required' => true], ['label' => 'Nome']) }}
                                    </div>
                                    <div class="form-group col-6">
                                        <div class="col-12">
                                            <div class="form-group col">
                                                <label for="budget_id">Orçamento</label>
                                                <select class="selectpicker form-control form-control-sm" name="budget_id"
                                                    id="budget_id" data-actions-box="true" data-live-search="true" required>

                                                </select>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row">
                                    <div class="form-group col-11">
                                        <label for="description" class="ml-3">Descrição</label>
                                        <textarea name="description" class="form-control ml-3" id="description" cols="30" rows="10" required></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group  col">


                                        <button type="submit" class="create-event btn ml-3 btn-success mb-3 ml-3">
                                            @icon('fas fa-plus-circle')
                                            @lang('common.create')
                                        </button>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="content-panel" style="padding:0">
        @include('GA::budget.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item active" aria-current="page">
                                    Orçamentos
                                </li>
                            </ol>
                        </div>

                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-sm-6">
                        <h1> ORÇAMENTOS</h1>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        @if (auth()->user()->hasAnyPermission(['manage-budget']))
                            <a href="{{ route('budget.create') }}" class="btn btn-success  mb-3">
                                @icon('fas fa-plus-square')
                                @lang('common.new')
                            </a>
                        @endif
                        {{-- <button type="button" class="btn alert-primary" data-toggle="modal" data-target="#ModalSession">
                            <i class="fas fa-key"></i> Terminar sessão
                          </button> --}}


                        <table id="budget-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Tipo</th>
                                    <th>Capítulos</th>
                                    {{-- <th>Total</th> --}}
                                    <th>Estado</th>
                                    <th>Criado por</th>
                                    <th>Criado a</th>
                                    <th>Actualizado por</th>
                                    <th>Actualizado a</th>
                                    <th>Actividade</th>
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
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> --}}

    <script>
        $(function() {
            $('#budget-table').DataTable({
                ajax: '{!! route('budget.ajax') !!}',
                searching: true,
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
                        name: 'name'
                    }, { 
                        data: 'description',
                        name: 'description'
                    }, {
                        data: 'type',
                        name: 'type'
                    }, {
                        data: 'chapters',
                        name: 'chapters'
                    }, {
                        data: 'money',
                        name: 'money'
                    }
                    // , {
                    //     data: 'state',
                    //     name: 'state'
                    // }
                    , {
                        data: 'created_by',
                        name: 'created_by',
                        visible: false
                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        visible: false
                    }, {
                        data: 'updated_by',
                        name: 'updated_by',
                        visible: false
                    }, {
                        data: 'updated_at',
                        name: 'updated_at',
                        visible: false
                    }, {
                        data: 'actions',
                        name: 'actions'

                    }
                ],

                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }
            });
        });
    </script>

    <script>
        $(".modal-footer form div").hide();

        function pegar(element) {

            var id = $(element).attr("data");
            id = parseInt(id);
            $("input[name='id']").val(id);
        }

        function novo(element) {



            var id = $(element).attr("data").split(",");

            $("#budget_id").html("");
            $("#budget_id").append("<option value='" + id[0] + "' selected>" + id[1] + "</option>")
            $(".titulo-capitulo").text("Criar capítulo ( Nº " + (parseInt(id[2]) + 1) + " )");
            $("#budget_id").selectpicker('refresh');
        }
    </script>
@endsection


<section>
    <div></div>
</section>
