@section('title',__('Disciplinas::Relatórios::ForLearn'))
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
                        <h1>Relatório de Disciplinas</h1>
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
                               <div id="group">
                                <table class="table table-striped table-hover data-table">
                                    <thead>
                                        <tr>
                                            <th>Nº Ordem</th>
                                            <th>ID</th>
                                            <th>Code</th>
                                            <th>Disciplina</th>
                                            <th>Abreviação</th>
                                            <th>Curso</th>
                                            <th>Área de Disciplina</th>
                                            <th>Perfil de Disciplina</th>
                                          </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($model as $discipline)
                                            <tr> 
                                                <td></td>
                                                <td>{{$discipline->discipline_id}} </td>
                                                <td>{{$discipline->discipline_code  }}</td>
                                                <td>{{$discipline->discipline_name}}</td>
                                                <td>{{$discipline->discipline_abbreviation }}</td>
                                                <td>{{$discipline->course_name }}</td>
                                                <td>{{$discipline->discipline_area }}</td>
                                                <td>{{$discipline->profile }}</td>
                                                </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                           
                            <div id="container" style="font-size:1;"></div>
                            </div>


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
@include('layouts.disciplinebackofficeScriptQueryBuilder')
@endsection
