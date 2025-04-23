@section('title',__('Utilizadores::Relatórios::ForLearn'))
@extends('layouts.backoffice_qbd')

@section('styles')
    @parent
@endsection

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Relatório de Pagamentos</h1>
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
                            <hr>
                            <div class="card">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <div id="builder-basic"></div>
                                            <button id="btn-get" style="width: 180px; background: #1e1e1e; color:#fff; padding:2px; border-color:#1e1e1e ; border-radius:7px;"><i class="fas fa-list-ul"></i> Gerar Relatório</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        <div class="card">
                            <div class="card-body">
                                
                                {{--
                               <div id="group">
                                <table class="table table-striped table-hover data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Cargo</th>
                                            <th>Criado a</th>
                                            <th>Atualizado a</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            --}}

                            <div id="container"></div>

                            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                 
                                  <div class="modal-body">
                                   <center> <img src="/img/loading_gf.gif" width="100px"> <b>A Gerar Relatórios...</b> </center>
                                  </div>

                              </div>
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
@include('layouts.paymentsbackofficeScriptQueryBuilder')
@endsection
