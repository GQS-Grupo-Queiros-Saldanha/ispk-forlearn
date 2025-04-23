@section('title', __('Requerimentos'))
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

        .modal-request .modal-content {
            border-radius: 10px !important;
            width: 1000px;
            margin: 0 auto;
        }

        .modal-button {
            border-radius: 5px;
        }

        .modal-request .modal-header {
            padding-left: 36px;
        }

        .modal-request .modal-title {
            padding: 10px 36px 10px 20px;
            border-left: 6px solid #e5842e;
            font-weight: 700;
        }

        .modal-request .modal-body textarea {

            font-size: 14px;
            width: 96%;
            margin-left: 2% !important;
            padding: 10px 10px 10px 10px;
        }

        .modal-request  span{
            color:black;
        }
    </style>


    <!-- Large modal -->


    <div class="modal fade bd-example-modal-lg modal-request" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 1000px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">REQEUERIMENTO</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-12">

                        <div class="row">
                            <div class="col-12 mb-1">
                          
                                <h5><span>DADOS DO ESTUDANTE</span></h5>
                                
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="student_name">Nome completo</label>
                                    <input type="text" class="form-control" required disabled>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="requerment">Matrícula</label>
                                    <input type="text" class="form-control" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-1">
                                
                                <h5><span>DADOS DO REQEUERIMENTO</span></h5>
                                
                            </div>

                        </div>
                        <div class="row">

                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="req_type">Tipo de requerimento</label>
                                    <select class="selectpicker form-control form-control-sm" name="req_type" id="req_type"
                                        data-actions-box="true" data-live-search="true">
                                        <option value="1">Avaliação</option>
                                        <option value="2">Documentos</option>
                                        <option value="3">Revisão de notas</option>
                                        <option value="4">Mudança de turno</option>
                                        <option value="5">Mudança de Curso</option>
                                        <option value="6">Transferência</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="requerment">Requerimento</label>
                                    <select class="selectpicker form-control form-control-sm" name="requerment"
                                        id="requerment" data-actions-box="true" data-live-search="true" disabled>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="req_object">Disciplina</label>
                                    <select class="selectpicker form-control form-control-sm" name="req_object"
                                        id="req_object" data-actions-box="true" data-live-search="true" disabled>

                                    </select>
                                </div>
                            </div>

                        </div>

                        <hr>

                    </div>
                    <textarea class="form-control" name="description_req" id="description_req"  cols="50" rows="15"
                        placeholder="Por favor informe o motivo da requisição..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success modal-button">@icon('fas fa-plus-circle') Requerer</button>
                    <button type="button" class="btn btn-secondary modal-button" data-dismiss="modal">@icon('fas fa-plus-close')
                        Cancelar</button>
                </div>

            </div>
        </div>
    </div>


    <div class="content-panel" style="padding:0">
        @include('GA::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-6">
                        <h1> Requerimentos</h1>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">



                    <div class="col-12">
                        <div class="row">

                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="req_type">Tipo de requerimento</label>
                                    <select class="selectpicker form-control form-control-sm" name="req_type" id="req_type"
                                        data-actions-box="true" data-live-search="true">
                                        <option value="1">Avaliação</option>
                                        <option value="2">Documentos</option>
                                        <option value="3">Revisão de notas</option>
                                        <option value="4">Mudança de turno</option>
                                        <option value="5">Mudança de Curso</option>
                                        <option value="6">Transferência</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="requerment">Requerimento</label>
                                    <select class="selectpicker form-control form-control-sm" name="requerment"
                                        id="requerment" data-actions-box="true" data-live-search="true" disabled>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="req_object">Disciplina</label>
                                    <select class="selectpicker form-control form-control-sm" name="req_object"
                                        id="req_object" data-actions-box="true" data-live-search="true" disabled>

                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-6">
                                <button type="submit" data-toggle="modal" data-target=".bd-example-modal-lg"
                                    class="create-event btn ml-3 btn-success mb-3 submit " style="border-radius:4px;">
                                    @icon('fas fa-check')
                                    Avançar
                                </button>


                            </div>
                            <div class="col-6">
                               
                            </div>
                        </div>
                        <br>
                        <br>
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
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>



@endsection
